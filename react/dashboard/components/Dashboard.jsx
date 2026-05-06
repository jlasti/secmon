import React, { useState, useEffect, useCallback } from 'react';
import { Responsive, WidthProvider } from 'react-grid-layout';
import WidgetCard from './WidgetCard';
import DashboardModal from './DashboardModal';
import api from '../services/api';
import 'react-grid-layout/css/styles.css';
import 'react-resizable/css/styles.css';
import './Dashboard.css';
import { debounce } from 'lodash';

const ResponsiveGridLayout = WidthProvider(Responsive);

const Dashboard = () => {
  const [currentDashboardId, setCurrentDashboardId] = useState("");
  const [widgets, setWidgets] = useState([]);
  const [refreshTime, setRefreshTime] = useState(0);
  const [refreshInterval, setRefreshInterval] = useState(null);
  const [dashboards, setDashboards] = useState([]);
  const [modalState, setModalState] = useState({
    isOpen: false,
    mode: 'create', // 'create' or 'edit'
    dashboard: null
  });
  const [deleteConfirmOpen, setDeleteConfirmOpen] = useState(false);
  const [layoutLoaded, setLayoutLoaded] = useState(false);
  const [isViewMode, setIsViewMode] = useState(false);


  useEffect(() => {
      const initialize = async () => {
          const dbs = await loadDashboards();
          if (dbs && dbs.length > 0) {
              const activeDashboardId = dbs.find(d => d.active)?.id || dbs[0].id;
              setCurrentDashboardId(activeDashboardId);
          }
      };
      initialize();
  }, []);

  useEffect(() => {
      if (currentDashboardId) {
          loadDashboard(currentDashboardId);
          const currentDb = dashboards.find(d => d.id === parseInt(currentDashboardId));
          setRefreshTime(parseRefreshTime(currentDb?.refresh_time));
          setLayoutLoaded(false);
      }
  }, [currentDashboardId, dashboards]);


  const loadDashboard = async (dashboardId, changeActive = true) => {
    try {
      if (changeActive) {
        const data = await api.changeActiveDashboard(dashboardId);
        if(data){
          setWidgets(data);
          setLayoutLoaded(true);
        }
      }
    } catch (error) {
      console.error('Error loading dashboard:', error);
    }
  };

  const loadDashboards = async() => {
    try {
      const data = await api.getDashboards();
      if(data){
        setDashboards(data);
        return data;
      }
      return [];
    } catch (error) {
      console.error('Error loading dashboard:', error);
      return [];
    }
  };

  const parseRefreshTime = (refreshString) => {
    if (!refreshString || refreshString === '0') return 0;

    const unit = refreshString.slice(-1);
    const value = parseInt(refreshString.slice(0, -1));
    
    const multipliers = {
      'S': 1,
      'm': 60,
      'H': 3600,
      'D': 86400,
      'W': 604800,
      'M': 2592000,
      'Y': 31536000
    };
    
    return value * (multipliers[unit] || 1);
  };

  const saveLayoutsToBackend = async (dashboardId, widgetsPositionalInformation) => {
        try {
            await api.updateWidgetLayouts(dashboardId, widgetsPositionalInformation);
        } catch (error) {
            console.error('Error updating layout:', error);
        }
    };

  const debouncedSaveLayout = useCallback(
        debounce((widgetsPositionalInformation) => {
            saveLayoutsToBackend(currentDashboardId, widgetsPositionalInformation);
        }, 1000), 
        [currentDashboardId]
    );

    useEffect(() => {
        return () => {
            debouncedSaveLayout.cancel();
        };
    }, [debouncedSaveLayout]);

  const handleCreateDashboard = () => {
    setModalState({
      isOpen: true,
      mode: 'create',
      dashboard: null
    });
  };

  const handleUpdateDashboard = () => {
    const currentDashboard = dashboards.find(d => d.id === parseInt(currentDashboardId));
    if (currentDashboard) {
      setModalState({
        isOpen: true,
        mode: 'edit',
        dashboard: currentDashboard
      });
    }
  };

  const handleDeleteDashboard = () => {
    if (dashboards.length === 1) {
      alert('Cannot delete the last dashboard');
      return;
    }
    setDeleteConfirmOpen(true);
  };

  const confirmDelete = async () => {
    try {
      await api.deleteDashboard(currentDashboardId);
      const updatedDashboards = await api.getDashboards();
      setDashboards(updatedDashboards);
      const newActiveDashboard = updatedDashboards[0];
      if (newActiveDashboard) {
        setCurrentDashboardId(newActiveDashboard.id);
      }
      setDeleteConfirmOpen(false);
    } catch (error) {
      console.error('Error deleting dashboard:', error);
      alert('Failed to delete dashboard');
    }
  };

  const handleModalSubmit = async (formData) => {
    try {
      if (modalState.mode === 'create') {
        const newDashboard = await api.createDashboard(formData);
        const updatedDashboards = await api.getDashboards();
        setDashboards(updatedDashboards);
        setCurrentDashboardId(newDashboard.id);
      } else if (modalState.mode === 'edit') {
        await api.updateDashboard(currentDashboardId, formData);
        const updatedDashboards = await api.getDashboards();
        setDashboards(updatedDashboards);
        setRefreshTime(parseRefreshTime(formData.refresh_time));
      }
    } catch (error) {
      console.error('Error saving dashboard:', error);
      alert('Failed to save dashboard');
    }
  };

  const handleAddWidget = async () => {
    try {
      const result = await api.createWidget(
        currentDashboardId, 
        {title: 'New Widget', chart_type: null}  
      );
      if (result && result.widget) {
        setWidgets(prevWidgets => [...prevWidgets, result.widget]);
      }
    } catch (error) {
      console.error('Error adding widget:', error);
    }
  };

  const handleWidgetUpdate = (updatedWidget) => {
    setWidgets(prevWidgets =>
      prevWidgets.map(w => {
        if (w.id === updatedWidget.id) {
          return {
            // keep existing positional/layout info, but update content
            ...w,
            ...updatedWidget,
            layout: w.layout ?? updatedWidget.layout
          };
        }
        return w;
      })
    );
    return 0;
  };

  const handleWidgetDelete = (widgetId) => {
    setWidgets(widgets.filter(w => w.id !== widgetId));
  };

  const handleLayoutChange = async (layout) => {
    if (!layoutLoaded) {
      return;
    }
    const widgetsPositionalInformation = layout.map(item => ({
      widget_id: parseInt(item.i),
      x: item.x,
      y: item.y,
      w: item.w,
      h: item.h
    }));
    setWidgets(prevWidgets => 
      prevWidgets.map(widget => {
        const layoutItem = layout.find(item => parseInt(item.i) === widget.id);
        if (layoutItem) {
          return {
            ...widget,
            layout: {
              x: layoutItem.x,
              y: layoutItem.y,
              w: layoutItem.w,
              h: layoutItem.h
            }
          };
        }
        return widget;
      })
    );
    
    debouncedSaveLayout(widgetsPositionalInformation);
  };

  const getLayout = () => {
    return widgets.map((widget, index) => {
      const layout = widget.layout || '{}';
      if (layout && typeof layout === 'object') {
        return {
          i: widget.id.toString(),
          x: layout.x ?? (index % 4) * 3,
          y: layout.y ?? Math.floor(index / 4) * 4,
          w: layout.w ?? 4,
          h: layout.h ?? 4,
          minW: 3,
          minH: 3
        };
      }
      return {
        i: widget.id.toString(),
        x: (index % 4) * 3,
        y: Math.floor(index / 4) * 4,
        w: 4,
        h: 4,
        minW: 3,
        minH: 3
      };
    });
  };

  const visibleWidgets = widgets.filter(w => w.dashboard_id === parseInt(currentDashboardId));


  return (
    <div className={`dashboard-container ${isViewMode ? 'header-hidden' : ''}`}>
      <div className="dashboard-header">
        <div className="header-left">
          <button 
            className="dashboard-action-btn back-btn"
            onClick={() => window.history.back()}
            title="Back to Main App"
          >
            <span>← Back</span>
          </button>

          <div className="dashboard-select-container">
            <label className="dashboard-select-label">
              Dashboards
            </label>
            <div className="dashboard-tabs-container">
              {dashboards.map(dashboard => (
                <button
                  key={dashboard.id}
                  className={`dashboard-tab ${parseInt(currentDashboardId) === dashboard.id ? 'active' : ''}`}
                  onClick={() => setCurrentDashboardId(dashboard.id)}
                  title={dashboard.name}
                >
                  {dashboard.name}
                </button>
              ))}
              <button
                className="add-dashboard-tab"
                onClick={handleCreateDashboard}
                title="Create New Dashboard"
                disabled={isViewMode}
              >
                +
              </button>
            </div>
          </div>
        </div>
        
        <div className="dashboard-actions">
          <button 
            className="dashboard-action-btn collapse-header-btn"
            onClick={() => setIsViewMode(!isViewMode)}
            title={isViewMode ? "Show Header" : "Hide Header"}
          >
            <span className={`collapse-arrow ${isViewMode ? 'expanded' : ''}`}>▲</span>
          </button>
          
          <button 
            className="dashboard-action-btn update-btn icon-btn"
            onClick={handleUpdateDashboard}
            title="Edit Dashboard"
            aria-label="Edit Dashboard"
            disabled={isViewMode}
          >
            <span className="action-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#e3e3e3" focusable="false">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
              </svg>
            </span>
          </button>
          
          <button 
            className="dashboard-action-btn delete-btn icon-btn"
            onClick={handleDeleteDashboard}
            title="Delete Dashboard"
            aria-label="Delete Dashboard"
            disabled={isViewMode}
          >
            <span className="action-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#e3e3e3" focusable="false">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
              </svg>
            </span>
          </button>
        </div>
      </div>

      <div className="dashboard-grid">
        <ResponsiveGridLayout
          className="layout"
          layouts={{ lg: getLayout() }}
          breakpoints={{ lg: 1200, md: 996}}
          cols={{ lg: 12, md: 12}}
          rowHeight={80}
          onLayoutChange={handleLayoutChange}
          draggableHandle=".widget-drag-handle"
          compactType="vertical"
          isDraggable={!isViewMode}
          isResizable={!isViewMode}
          static={isViewMode}
        >
          {visibleWidgets.map(widget => (
            <div key={widget.id.toString()}>
              <WidgetCard
                widget={widget}
                refreshInterval={refreshTime}
                onWidgetUpdate={handleWidgetUpdate}
                onDelete={handleWidgetDelete}
                isViewMode={isViewMode}
              />
            </div>
          ))}
        </ResponsiveGridLayout>
      </div>

      {isViewMode && (
        <>
          <div className="view-mode-hover-zone" aria-hidden="true"></div>
          <button 
            className="show-header-fab"
            onClick={() => setIsViewMode(false)}
            title="Show Header"
          >
            ▼
          </button>
        </>
      )}

      {!isViewMode && (
        <button 
          className="add-widget-button"
          onClick={handleAddWidget}
          title="Add New Widget"
        >
          <span className="add-widget-icon">+</span>
          <span className="add-widget-text">Add Widget</span>
        </button>
      )}

      <DashboardModal
        isOpen={modalState.isOpen}
        onClose={() => setModalState({ ...modalState, isOpen: false })}
        onSubmit={handleModalSubmit}
        dashboard={modalState.dashboard}
        mode={modalState.mode}
      />

      {deleteConfirmOpen && (
        <div className="modal-overlay" onClick={() => setDeleteConfirmOpen(false)}>
          <div className="modal-content dashboard-modal-content delete-confirm" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h2>Confirm Delete</h2>
              <button className="modal-close-btn" onClick={() => setDeleteConfirmOpen(false)}>
              </button>
            </div>
            <div className="modal-form">
              <p>Are you sure you want to delete this dashboard? This action cannot be undone.</p>
              <div className="modal-actions">
                <button className="btn-secondary" onClick={() => setDeleteConfirmOpen(false)}>
                  Cancel
                </button>
                <button className="btn-danger" onClick={confirmDelete}>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Dashboard;
