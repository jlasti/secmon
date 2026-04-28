import React, { useState, useEffect, useRef } from 'react';
import BarChart from './charts/BarChart';
import PieChart from './charts/PieChart';
import LineChart from './charts/LineChart';
import EventTable from './charts/EventTable';
import GeoMap from './charts/GeoMap';
import WidgetSettings from './WidgetSettings';
import api from '../services/api';
import './WidgetCard.css';
import ReactDOM from 'react-dom';

const WidgetCard = ({ 
  widget, 
  refreshInterval = 0,
  onWidgetUpdate, 
  onDelete,
  isViewMode = false
}) => {
  const [isLoading, setIsLoading] = useState(false);
  const [contentData, setContentData] = useState(null);
  const [showSettings, setShowSettings] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [pollInterval, setPollInterval] = useState(null);
  const lastFetchedIdRef = useRef(null);
  const currentPageRef = useRef(1);

  const config = JSON.parse(widget.config || '{}');
  const hasContent = widget.chart_type !== null && widget.chart_type !== undefined && widget.chart_type !== '';

  // Reset page to 1 when chart type or config changes
  useEffect(() => {
    setCurrentPage(1);
  }, [widget.chart_type, widget.config]);

  // Fetch data when page changes (for tables only)
  useEffect(() => {
    currentPageRef.current = currentPage;
    if (hasContent && widget.chart_type === 'table') {
      loadContent(false);
    }
  }, [currentPage]);

  // Initial load and setup polling
  useEffect(() => {
    if (hasContent) {
      // Initial load
      loadContent(true);

      if (refreshInterval > 0) {
        const interval = setInterval(() => {
          if (widget.chart_type === 'table') {
            loadTablePage1();
          } else {
            loadContent(false);
          }
        }, refreshInterval * 1000);
        setPollInterval(interval);

        return () => clearInterval(interval);
      }
    } else {
      // Clear polling if no content
      if (pollInterval) {
        clearInterval(pollInterval);
        setPollInterval(null);
      }
    }
  }, [widget.id, widget.filter_id, widget.timeframe, widget.chart_type, widget.config, refreshInterval]);

  // Helper to fetch page 1 without changing user's current page
  const loadTablePage1 = async () => {
    try {
      const data = await api.getWidgetContent(widget.id, 1, null);
      if (data) {
        // Only update if user is on page 1, otherwise keep their current page data
        if (currentPageRef.current === 1) {
          setContentData(data);
        }
      }
    } catch (error) {
      console.error('Error refreshing table:', error);
    }
  };

  // Merge delta data based on chart type
  const mergeData = (existingData, newDeltaData, chartType) => {
    if (!newDeltaData || newDeltaData.length === 0) {
      return existingData;
    }

    switch (chartType) {
      case 'pieChart':
      case 'barChart':
        // For pie/bar charts, merge by label/field
        return mergeChartData(existingData, newDeltaData);
      
      case 'lineChart':
        // For line charts, merge by x value (timestamp)
        return mergeLineChartData(existingData, newDeltaData);
      
      case 'table':
        // For tables, prepend new rows (most recent first)
        return [...newDeltaData, ...existingData];
      
      case 'geoMap':
        // For geo maps, merge by code
        return mergeGeoData(existingData, newDeltaData);
      
      default:
        return existingData;
    }
  };

  // Merge pie/bar chart data by aggregating counts
  const mergeChartData = (existing, delta) => {
    const merged = [...existing];
    
    delta.forEach(newItem => {
      // For bar/pie charts, match strictly by label to avoid cross-contamination
      const matchKey = newItem.label !== undefined ? 'label' : (newItem.x !== undefined ? 'x' : 'name');
      const matchValue = newItem[matchKey];
      
      const existingItem = merged.find(item => item[matchKey] === matchValue);
      
      if (existingItem) {
        // Add to existing count
        existingItem.count = (existingItem.count || 0) + (newItem.count || 0);
        existingItem.y = (existingItem.y || 0) + (newItem.y || 0);
      } else {
        // New data point
        merged.push(newItem);
      }
    });
    
    return merged;
  };

  // Merge line chart data by time bucket
  const mergeLineChartData = (existing, delta) => {
    const merged = [...existing];
    
    delta.forEach(newItem => {
      const existingItemIndex = merged.findIndex(item => item.x === newItem.x);
      
      if (existingItemIndex !== -1) {
        // Update existing y value
        merged[existingItemIndex].y = (merged[existingItemIndex].y || 0) + (newItem.y || 0);
      } else {
        // Append new time bucket (backend sends sorted, so new items come at end)
        merged.push(newItem);
      }
    });
    
    return merged;
  };

  // Merge geo map data by country code
  const mergeGeoData = (existing, delta) => {
    const merged = [...existing];
    
    delta.forEach(newItem => {
      const existingItem = merged.find(item => item.code === newItem.code);
      
      if (existingItem) {
        // Add to existing count
        existingItem.count = (existingItem.count || 0) + (newItem.count || 0);
      } else {
        // New country
        merged.push(newItem);
      }
    });
    
    // Sort by count descending
    return merged.sort((a, b) => b.count - a.count);
  };

  const loadContent = async (isInitial = false) => {
    if (!isInitial) {
      setIsLoading(false);
    } else {
      setIsLoading(true);
    }

    try {
      const pageParam = widget.chart_type === 'table' ? Number(currentPage) || 1 : null;
      const lastId = (widget.chart_type === 'table' || isInitial) ? null : lastFetchedIdRef.current;

      const data = await api.getWidgetContent(widget.id, pageParam, lastId);
      
      if (data) {
        if (widget.chart_type !== 'table' && data.lastId !== undefined && data.lastId !== null) {
          lastFetchedIdRef.current = Number(data.lastId);
        }

        if (isInitial) {
          setContentData(data);
        } else {
          if (widget.chart_type === 'table') {
            setContentData(data);
          } else {
            setContentData(prevData => {
              if (!prevData) return data;
              
              return {
                ...data,
                data: mergeData(prevData.data || [], data.data || [], widget.chart_type)
              };
            });
          }
        }
      }
    } catch (error) {
      console.error('Error loading content:', error);
    } finally {
      if (isInitial) {
        setIsLoading(false);
      }
    }
  };

  const handleSaveSettings = async (newConfig) => {
    try {
      const result = await api.updateWidgetSettings(newConfig);
      if (result.success && result.widget) {
        onWidgetUpdate(result.widget);
        setShowSettings(false);
        lastFetchedIdRef.current = null;
      }
    } catch (error) {
      console.error('Error saving settings:', error);
    }
  };

  const handleDelete = async () => {
      try {
        await api.deleteWidget(widget.id);
        onDelete(widget.id);
      } catch (error) {
        console.error('Error deleting widget:', error);
      }
  };

  const renderContent = () => {
    if (!hasContent) {
      if (isViewMode) {
        return (
          <div className="widget-empty-state">
            {/* <p style={{ color: '#999', fontSize: '14px' }}>No content</p> */}
          </div>
        );
      }
      return (
        <div className="widget-empty-state">
          <button 
            className="add-content-btn"
            onClick={() => setShowSettings(true)}
          >
            Add content
          </button>
        </div>
      );
    }

    if (isLoading) {
      return (
        <div className="widget-loader">
          <div className="spinner"></div>
          <p>Loading...</p>
        </div>
      );
    }

    if (!contentData) {
      return <div className="widget-error">Failed to load content</div>;
    }

    const chartType = widget.chart_type;

    switch (chartType) {
      case 'barChart':
        return <BarChart data={contentData.data} config={config} />;
      case 'pieChart':
        return <PieChart data={contentData.data} config={config} />;
      case 'lineChart':
        return <LineChart data={contentData.data} />;
      case 'table':
        return (
          <EventTable 
            data={contentData.data}
            columns={contentData.columns}
            pagination={contentData.pagination}
            onPageChange={(page) => setCurrentPage(Number(page))}
          />
        );
      case 'geoMap':
        return <GeoMap data={contentData.data} />;
      default:
        return <div>Unknown content type</div>;
    }
  };

  return (
    <>
      <div className="widget-card">
        <div className={`widget-header ${isViewMode ? 'view-mode' : ''}`}>
          <div className="widget-drag-handle">
            <h3 className="widget-title">{widget.title || 'New Widget'}</h3>
          </div>
          {!isViewMode && (
            <div className="widget-actions">
              <button 
                className="widget-action-btn settings"
                onClick={() => setShowSettings(true)}
                title="Settings"
              >
                🔧
              </button>
            </div>
          )}
        </div>
        <div className="widget-content">
          {renderContent()}
        </div>
      </div>

      {showSettings && (
        ReactDOM.createPortal(
        <WidgetSettings
          widget={widget}
          config={config}
          onSave={handleSaveSettings}
          onDelete={handleDelete}
          onClose={() => setShowSettings(false)}
        />, document.getElementById('react-dashboard-root'))
      )}
    </>
  );
};

export default WidgetCard;
