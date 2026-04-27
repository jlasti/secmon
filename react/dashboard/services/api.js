import axios from 'axios';

const API_BASE_URL = '/api';

class DashboardAPI {
  constructor() {
    this.apiClient = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
      },
      withCredentials: true,  // Send session cookies
    });

    this.setupAxiosInterceptors();
  }

setupAxiosInterceptors() {
  this.apiClient.interceptors.request.use(
    (config) => {
      const method = (config.method || 'get').toLowerCase();
      
      // CSRF protection is generally not required for safe methods (GET, HEAD, OPTIONS)
      const unsafeMethods = ['post', 'put', 'patch', 'delete'];

      if (unsafeMethods.includes(method)) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (csrfToken) {
          config.headers = config.headers || {};
          config.headers['X-CSRF-Token'] = csrfToken;
        }
      }

      return config;
    },
    (error) => Promise.reject(error)
  );
}

  async getDashboards() {
    try {
      const response = await this.apiClient.get('/dashboard/dashboards');
      return response.data;
    } catch (error) {
      console.error('Error getting dashboards:', error);
      throw error;
    }
  }

  async getDashboard(id) {
    try {
      const response = await this.apiClient.get(`/dashboard/dashboard/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error getting dashboard ${id}:`, error);
      throw error;
    }
  }

  async createDashboard(data) {
    try {
      const response = await this.apiClient.post('/dashboard/create', data);
      return response.data;
    } catch (error) {
      console.error('Error creating dashboard:', error);
      throw error;
    }
  }

  async updateDashboard(id, data) {
    try {
      const response = await this.apiClient.put(`/dashboard/${id}`, data);
      return response.data;
    } catch (error) {
      console.error(`Error updating dashboard ${id}:`, error);
      throw error;
    }
  }

  async deleteDashboard(id) {
    try {
      const response = await this.apiClient.delete(`/dashboard/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting dashboard ${id}:`, error);
      throw error;
    }
  }
  

  async changeActiveDashboard(newId) {
      try {
          const response = await this.apiClient.post('/dashboard/change-active', { newDashboardId: newId }); 
          return response.data; 
      } catch (error) {
          console.error('Error changing active dashboard:', error);
          throw error;
      }
  }

  async createWidget(dashboard_id, settings) {
    try {
      const response = await this.apiClient.post('/dashboard/create-widget', {
        dashboard_id: dashboard_id,
        title: settings.title,
        chart_type: null,
        config: JSON.stringify(settings.config), 
      });
      return response.data;
    } catch (error) {
      console.error('Error creating widget:', error);
      throw error;
    }
  }

  async updateWidgetSettings(settings) {
    try {
      const response = await this.apiClient.post(
        `/dashboard-widget/update-settings`, 
        {
          widget_id: settings.widget_id,
          title: settings.title,
          chart_type: settings.chart_type,
          dashboard_id: settings.dashboard_id,
          filter_id: settings.filter_id,
          timeframe: settings.timeframe,
          config: settings.config ? JSON.stringify(settings.config) : undefined
        }
      );
      return response.data;
    } catch (error) {
      console.error(`Error updating widget settings`);
      throw error;
    }
  }

  async deleteWidget(widgetId) {
    try {
      const response = await this.apiClient.delete(`/dashboard/delete-widget?widgetId=${widgetId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting widget ${widgetId}:`, error);
      throw error;
    }
  }

  async updateWidgetLayouts(dashboardId, widgetsPositionalInformation) {
    try {
      const response = await this.apiClient.post('/dashboard/update-widget-layout', {
        dashboard_id: dashboardId,
        widgetsPositionalInformation: widgetsPositionalInformation
      });
      return response.data;
    } catch (error) {
      console.error('Error updating widget layouts:', error);
      throw error;
    }
  }

  async getFilters() {
    try {
      const response = await this.apiClient.get('/dashboard/filters');
      return response.data;
    } catch (error) {
      console.error('Error getting filters:', error);
      throw error;
    }
  }

  async getAllSecurityEventFields() {
    try {
      const response = await this.apiClient.get('/dashboard-widget/all-security-event-fields');
      return response.data;
    } catch (error) {
      console.error('Error getting filters:', error);
      throw error;
    }
  }


  async getWidgetContent(widgetId, page = null, lastId = null) {
    try {
      const response = await this.apiClient.get('/dashboard-widget/content', {
        params: {
          widgetId,
          ...(page !== null ? { pagination: page } : {}),
          ...(lastId !== null ? { lastId } : {})
        }
      });
      return response.data;
    } catch (error) {
      console.error('Error getting widget content:', error);
      throw error;
    }
  }
}

export default new DashboardAPI();