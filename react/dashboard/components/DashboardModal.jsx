import React, { useState, useEffect, useRef } from 'react';
import './DashboardModal.css';

const DashboardModal = ({ isOpen, onClose, onSubmit, dashboard, mode }) => {
  const [formData, setFormData] = useState({
    name: '',
    refresh_time: '5S'
  });

  const [errors, setErrors] = useState({});
  const closeFromOverlayClickRef = useRef(false);

  useEffect(() => {
    if (dashboard && mode === 'edit') {
      setFormData({
        name: dashboard.name || '',
        refresh_time: dashboard.refresh_time || '5S'
      });
    } else if (mode === 'create') {
      setFormData({
        name: '',
        refresh_time: '5S'
      });
    }
  }, [dashboard, mode, isOpen]);

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.name.trim()) {
      newErrors.name = 'Dashboard name is required';
    }
    
    const refreshPattern = /^\d{1,5}[YMWDHmS]{1}$/;
    if (!formData.refresh_time || !refreshPattern.test(formData.refresh_time)) {
      newErrors.refresh_time = 'Enter valid format (nY/nM/nW/nD/nH/nm/nS)';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    
    if (validateForm()) {
      onSubmit(formData);
      handleClose();
    }
  };

  const handleClose = () => {
    setFormData({ name: '', refresh_time: '5S' });
    setErrors({});
    onClose();
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const handleOverlayMouseDown = (e) => {
    closeFromOverlayClickRef.current = e.target === e.currentTarget;
  };

  const handleOverlayClick = (e) => {
    if (e.target === e.currentTarget && closeFromOverlayClickRef.current) {
      handleClose();
    }
    closeFromOverlayClickRef.current = false;
  };

  if (!isOpen) return null;

  const modalTitle = mode === 'create' ? 'Create New Dashboard' : 'Edit Dashboard';
  const submitButtonText = mode === 'create' ? 'Create' : 'Update';

  return (
    <div
      className="modal-overlay"
      onMouseDown={handleOverlayMouseDown}
      onClick={handleOverlayClick}
    >
      <div className="modal-content dashboard-modal-content" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h2>{modalTitle}</h2>
          <button className="modal-close-btn" onClick={handleClose}>
          </button>
        </div>
        
        <form onSubmit={handleSubmit} className="modal-form">
          <div className="form-group">
            <label htmlFor="name">Dashboard Name</label>
            <input
              type="text"
              id="name"
              name="name"
              value={formData.name}
              onChange={handleChange}
              className={errors.name ? 'error' : ''}
              placeholder="Enter dashboard name"
              autoFocus
            />
            {errors.name && <span className="error-message">{errors.name}</span>}
          </div>
          
          <div className="form-group">
            <label htmlFor="refresh_time">Refresh Time</label>
            <input
              type="text"
              id="refresh_time"
              name="refresh_time"
              value={formData.refresh_time}
              onChange={handleChange}
              className={errors.refresh_time ? 'error' : ''}
              placeholder="e.g., 5S, 5m, 1H"
            />
            {errors.refresh_time && <span className="error-message">{errors.refresh_time}</span>}
            <small className="form-hint">
              Format: number + unit (S=seconds, m=minutes, H=hours, D=days, W=weeks, M=months, Y=years)
            </small>
          </div>
          
          <div className="modal-actions">
            <button type="button" className="btn-secondary" onClick={handleClose}>
              Cancel
            </button>
            <button type="submit" className="btn-primary">
              {submitButtonText}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default DashboardModal;
