import React from 'react';
import ReactDOM from 'react-dom/client';
import Dashboard from './components/Dashboard';
import './index.css';
import api from './services/api';

const initDashboard = async () => {
  const container = document.getElementById('react-dashboard-root');
  if (!container) {
    console.error('Dashboard container not found!');
    return;
  }
  const root = ReactDOM.createRoot(container);
  root.render(<Dashboard />);
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDashboard);
} else {
  initDashboard();
}

export default initDashboard;
