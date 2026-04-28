import React, { useRef, useState } from 'react';
import Select from 'react-select';
import { useWidgetSettingsForm } from '../hooks/useWidgetSettingsForm';
import './WidgetSettings.css';

const WidgetSettings = ({ widget, config, onSave, onDelete, onClose }) => {
  const {
    formData,
    filters,
    isLoadingFilters,
    availableVariables,
    isLoadingVariables,
    updateFormField,
    updateConfigField,
    getSubmitPayload
  } = useWidgetSettingsForm(widget, config);

  const [activeTab, setActiveTab] = useState('all');
  const [timeframeMode, setTimeframeMode] = useState('preset');
  const closeFromOverlayClickRef = useRef(false);
  const chartTypes = {
    barChart: { label: 'Bar Chart', icon: 'chart-icon-bar', category: 'categorical' },
    pieChart: { label: 'Pie Chart', icon: 'chart-icon-pie', category: 'categorical' },
    lineChart: { label: 'Line Chart', icon: 'chart-icon-line', category: 'continuous' },
    table: { label: 'Table', icon: 'chart-icon-table', category: 'all' },
    geoMap: { label: 'Geo Map', icon: 'chart-icon-map', category: 'location' }
  };

  const getVisibleChartTypes = () => {
    if (activeTab === 'all') {
      return Object.entries(chartTypes);
    }
    return Object.entries(chartTypes).filter(([_, chart]) => 
      chart.category === activeTab
    );
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSave(getSubmitPayload());
  };

  const handleOverlayMouseDown = (e) => {
    closeFromOverlayClickRef.current = e.target === e.currentTarget;
  };

  const handleOverlayClick = (e) => {
    if (e.target === e.currentTarget && closeFromOverlayClickRef.current) {
      onClose();
    }
    closeFromOverlayClickRef.current = false;
  };

  const isChartSelected = formData.chartType !== '';

  return (
    <div
      className="modal-overlay"
      onMouseDown={handleOverlayMouseDown}
      onClick={handleOverlayClick}
    >
      <div className={`modal-content widget-settings-modal-content ${isChartSelected ? 'expanded' : ''}`} onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h4>{formData.title || 'Widget'} - Options</h4>
          <button type="button" className="modal-close-btn" onClick={onClose} aria-label="Close">
            ×
          </button>
        </div>
        
        <form onSubmit={handleSubmit}>
          <div className={`modal-body-container ${isChartSelected ? 'two-panel' : ''}`}>
            <div className="settings-panel-left">
              <div className="form-group">
                <label htmlFor="widgetTitle">Title</label>
                <input
                  id="widgetTitle"
                  type="text"
                  value={formData.title}
                  onChange={(e) => updateFormField('title', e.target.value)}
                  placeholder="Widget title"
                  required
                />
              </div>

              <div className="form-group">
                <label htmlFor="widgetFilter">Filter</label>
                {isLoadingFilters ? (
                  <div>Loading filters...</div>
                ) : (
                  <select 
                    id="widgetFilter"
                    value={formData.filterId} 
                    onChange={(e) => updateFormField('filterId', e.target.value)}
                  >
                    <option value="">No Filter</option>
                    {filters.map(filter => (
                      <option key={filter.id} value={filter.id}>
                        {filter.name}
                      </option>
                    ))}
                  </select>
                )}
              </div>

              <div className="form-group">
                <label>Chart Type</label>
                
                {/* Tabs for filtering chart types */}
                <div className="widget-settings-tabs">
                  <button
                    type="button"
                    className={`widget-settings-tab ${activeTab === 'all' ? 'active' : ''}`}
                    onClick={() => setActiveTab('all')}
                  >
                    All
                  </button>
                  <button
                    type="button"
                    className={`widget-settings-tab ${activeTab === 'categorical' ? 'active' : ''}`}
                    onClick={() => setActiveTab('categorical')}
                  >
                    Categorical
                  </button>
                  <button
                    type="button"
                    className={`widget-settings-tab ${activeTab === 'continuous' ? 'active' : ''}`}
                    onClick={() => setActiveTab('continuous')}
                  >
                    Continuous
                  </button>
                  <button
                    type="button"
                    className={`widget-settings-tab ${activeTab === 'location' ? 'active' : ''}`}
                    onClick={() => setActiveTab('location')}
                  >
                    Location
                  </button>
                </div>

                <div className="chart-type-selector">
                  {getVisibleChartTypes().map(([type, chart]) => (
                    <div
                      key={type}
                      className={`chart-type-option ${formData.chartType === type ? 'selected' : ''}`}
                      onClick={() => updateFormField('chartType', type)}
                    >
                      <div className={`chart-type-icon ${chart.icon}`}></div>
                      <div className="chart-type-label">{chart.label}</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {isChartSelected && (
              <div className="settings-panel-right">
                <div className="panel-title">Chart Configuration</div>

                <div className="form-group">
                  <label htmlFor="widgetTimeframe">Timeframe</label>
                  <div className="timeframe-mode-toggle">
                    <button
                      type="button"
                      className={`timeframe-mode-btn ${timeframeMode === 'preset' ? 'active' : ''}`}
                      onClick={() => setTimeframeMode('preset')}
                    >
                      Presets
                    </button>
                    <button
                      type="button"
                      className={`timeframe-mode-btn ${timeframeMode === 'manual' ? 'active' : ''}`}
                      onClick={() => setTimeframeMode('manual')}
                    >
                      Manual
                    </button>
                  </div>

                  {timeframeMode === 'preset' && (
                    <div className="timeframe-slider-container">
                      <input
                        id="widgetTimeframe"
                        type="range"
                        min="0"
                        max="4"
                        value={['1D', '1W', '1M', '3M', '1Y'].indexOf(formData.timeframe)}
                        onChange={(e) => {
                          const timeframes = ['1D', '1W', '1M', '3M', '1Y'];
                          updateFormField('timeframe', timeframes[parseInt(e.target.value)]);
                        }}
                        className="timeframe-slider"
                      />
                      <div className="timeframe-labels">
                        <span>1 Day</span>
                        <span>1 Week</span>
                        <span>1 Month</span>
                        <span>3 Months</span>
                        <span>1 Year</span>
                      </div>
                      <div className="timeframe-value">
                        {formData.timeframe === '1D' && '1 Day'}
                        {formData.timeframe === '1W' && '1 Week'}
                        {formData.timeframe === '1M' && '1 Month'}
                        {formData.timeframe === '3M' && '3 Months'}
                        {formData.timeframe === '1Y' && '1 Year'}
                      </div>
                    </div>
                  )}

                  {timeframeMode === 'manual' && (
                    <div className="timeframe-manual-input-container">
                      <input
                        type="text"
                        value={formData.timeframe}
                        onChange={(e) => updateFormField('timeframe', e.target.value.toUpperCase())}
                        placeholder="e.g. 5D, 2W, 6M, 1Y"
                        maxLength="10"
                        className="timeframe-manual-input"
                      />
                      <span className="timeframe-manual-hint">Custom timeframe (5D, 2W, 6M, 1Y, etc.)</span>
                      <div className="timeframe-value">
                        {formData.timeframe}
                      </div>
                    </div>
                  )}
                </div>

                {/* Chart-specific config options */}
                {formData.chartType === 'pieChart' && (
                  <>
                    <div className="form-group">
                      <label htmlFor="pieChartVariable">Pie Chart Variable (Select One)</label>
                      {isLoadingVariables ? (
                        <div></div>
                      ) : (
                        <Select
                          id="pieChartVariable"
                          value={availableVariables
                            .map(v => ({ value: v, label: v }))
                            .find(option => option.value === formData.config.pie_chart_variable)}
                          onChange={(selected) => updateConfigField('pie_chart_variable', selected?.value || '')}
                          options={availableVariables.map(v => ({ value: v, label: v }))}
                          isClearable
                          placeholder="Search and select a variable..."
                          styles={{
                            control: (base) => ({
                              ...base,
                              minHeight: '38px'
                            })
                          }}
                        />
                      )}
                    </div>

                    <div className="form-group">
                      <label className="show-labels-row" htmlFor="showLabels">
                        <input
                          id="showLabels"
                          type="checkbox"
                          checked={formData.config.show_labels}
                          onChange={(e) => updateConfigField('show_labels', e.target.checked)}
                        />
                        Show Labels
                      </label>
                    </div>
                  </>
                )}

                {formData.chartType === 'barChart' && (
                  <div className="form-group">
                    <label htmlFor="barChartVariable">Bar Chart Variable (Select One)</label>
                    {isLoadingVariables ? (
                      <div>Loading variables...</div>
                    ) : (
                      <Select
                        id="barChartVariable"
                        value={availableVariables
                          .map(v => ({ value: v, label: v }))
                          .find(option => option.value === formData.config.bar_chart_variable)}
                        onChange={(selected) => updateConfigField('bar_chart_variable', selected?.value || '')}
                        options={availableVariables.map(v => ({ value: v, label: v }))}
                        isClearable
                        placeholder="Search and select a variable..."
                        styles={{
                          control: (base) => ({
                            ...base,
                            minHeight: '38px'
                          })
                        }}
                      />
                    )}
                  </div>
                )}

                {formData.chartType === 'table' && (
                  <div className="form-group">
                    <label htmlFor="tableColumns">Table Columns (Select Multiple)</label>
                    {isLoadingVariables ? (
                      <div>Loading columns...</div>
                    ) : (
                      <>
                        <Select
                          id="tableColumns"
                          value={formData.config.table_columns
                            .map(v => ({ value: v, label: v }))}
                          onChange={(selected) => updateConfigField('table_columns', selected ? selected.map(s => s.value) : [])}
                          options={availableVariables.map(v => ({ value: v, label: v }))}
                          isMulti
                          isClearable
                          placeholder="Search and select columns..."
                          styles={{
                            control: (base) => ({
                              ...base,
                              minHeight: '38px'
                            }),
                            menuPortal: (base) => ({ ...base, zIndex: 9999 })
                          }}
                          menuPortalTarget={document.body}
                        />
                        <small style={{ display: 'block', marginTop: '4px', color: '#666' }}>
                          Select multiple columns for your table
                        </small>
                      </>
                    )}
                  </div>
                )}

                {formData.chartType === 'geoMap' && (
                  <div className="form-group">
                    <label>Default Location Type</label>
                    <select
                      name="location_type"
                      value={formData.config.location_type || 'source'}
                      onChange={(e) => updateConfigField('location_type', e.target.value)}
                    >
                      <option value="source">Source Locations</option>
                      <option value="destination">Destination Locations</option>
                    </select>
                    <small style={{ display: 'block', color: '#666', marginTop: '4px' }}>
                      Choose whether to show source or destination address locations
                    </small>
                  </div>
                )}
              </div>
            )}
          </div>

          <div className="modal-footer">
            <button 
              type="button" 
              className="btn-danger"
              onClick={onDelete}
            >
              Delete Widget
            </button>
            <div className="modal-footer-right">
              <button type="submit" className="btn-primary">
                Save
              </button>
              <button type="button" className="btn-secondary" onClick={onClose}>
                Cancel
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};

export default WidgetSettings;
