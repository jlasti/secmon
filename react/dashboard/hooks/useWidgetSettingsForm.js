import { useState, useEffect } from 'react';
import api from '../services/api';

export const useWidgetSettingsForm = (widget, config) => {
  const [formData, setFormData] = useState({
    title: widget.title || '',
    chartType: widget.chart_type ,
    timeframe: widget.timeframe || '1D',
    filterId: widget.filter_id || '',
    dashboardId: widget.dashboard_id,
    config: {
      table_columns: config.table_columns || [],
      pie_chart_variable: config.pie_chart_variable || 'cef_severity',
      bar_chart_variable: config.bar_chart_variable || '',
      show_labels: config.show_labels !== undefined ? config.show_labels : true,
      location_type: config.location_type || 'source',
      ...config
    }
  });

  const [filters, setFilters] = useState([]);
  const [isLoadingFilters, setIsLoadingFilters] = useState(true);
  const [availableVariables, setAvailableVariables] = useState([]);
  const [isLoadingVariables, setIsLoadingVariables] = useState(false);

  useEffect(() => {
    loadFilters();
  }, []);

  useEffect(() => {
    if(availableVariables.length === 0)
    {
      loadAvailableVariables();
      }
  }, [formData.chartType]);

  const loadFilters = async () => {
    try {
      const filterData = await api.getFilters();
      setFilters(filterData || []);
    } catch (error) {
      console.error('Error loading filters:', error);
      setFilters([]);
    } finally {
      setIsLoadingFilters(false);
    }
  };

  const loadAvailableVariables = async () => {
    setIsLoadingVariables(true);
    try {
      const response = await api.getAllSecurityEventFields();
      const variables = response?.fields || [];
      setAvailableVariables(variables);
    } catch (error) {
      console.error('Error loading available variables:', error);
      setAvailableVariables([]);
    } finally {
      setIsLoadingVariables(false);
    }
  };

  const updateFormField = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const updateConfigField = (field, value) => {
    setFormData(prev => ({
      ...prev,
      config: { ...prev.config, [field]: value }
    }));
  };

  const buildConfigForSubmit = () => {
    let dynamicConfig = {};
    const currentConfig = formData.config;
    switch (formData.chartType) {
      case 'table':
        if (currentConfig.table_columns && currentConfig.table_columns.length > 0) {
          dynamicConfig.table_columns = currentConfig.table_columns;
        }
        break;
        
      case 'pieChart':
        dynamicConfig.pie_chart_variable = currentConfig.pie_chart_variable;
        dynamicConfig.show_labels = currentConfig.show_labels;
        break;

      case 'barChart':
        dynamicConfig.bar_chart_variable = currentConfig.bar_chart_variable;
        break;

      case 'geoMap':
        dynamicConfig.location_type = currentConfig.location_type || 'source';
        break;

      default:
        break;
    }

    return dynamicConfig;
  };

  const getSubmitPayload = () => ({
    widget_id: widget.id,
    title: formData.title,
    filter_id: formData.filterId,
    dashboard_id: formData.dashboardId,
    chart_type: formData.chartType,
    timeframe: formData.timeframe,
    config: buildConfigForSubmit()
  });

  return {
    formData,
    filters,
    isLoadingFilters,
    availableVariables,
    isLoadingVariables,
    updateFormField,
    updateConfigField,
    getSubmitPayload
  };
};
