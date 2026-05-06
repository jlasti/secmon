import React from 'react';
import './EventTable.css';

const EventTable = ({ 
  data = [], 
  columns = [],
  pagination = {},
  onPageChange = null
}) => {
  // Handle empty state
  if (!data || data.length === 0) {
    return (
      <div className="event-table-container">
        <div className="event-table-empty">
          <p>No events found</p>
        </div>
      </div>
    );
  }

  const handleRowClick = (eventId) => {
    window.location.href = `/security-events/view?id=${eventId}`;
  };

  const handlePrevious = () => {
    const currentPageNum = Number(pagination.page) || 1;
    if (currentPageNum > 1 && onPageChange) {
      onPageChange(currentPageNum - 1);
    }
  };

  const handleNext = () => {
    const currentPageNum = Number(pagination.page) || 1;
    const totalPages = Math.ceil(pagination.total / 10);
    if (currentPageNum < totalPages && onPageChange) {
      onPageChange(currentPageNum + 1);
    }
  };

  const totalPages = Math.ceil((Number(pagination.total) || 0) / 10);
  const hasData = data && Array.isArray(data) && data.length > 0;

  return (
    <div className="event-table-container">
      <div className="event-table-wrapper">
        <table className="event-table">
          <thead>
            <tr>
              {columns && columns.length > 0 && columns.map((column) => (
                <th key={column} className="event-table-header">
                  {formatColumnName(column)}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {hasData && data.map((row, idx) => (
              <tr 
                key={row.id || idx} 
                className="event-table-row"
                onClick={() => handleRowClick(row.id)}
              >
                {columns && columns.length > 0 && columns.map((column) => (
                  <td key={`${row.id || idx}-${column}`} className="event-table-cell">
                    {formatCellValue(row[column])}
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {pagination && pagination.total > 0 && (
        <div className="event-table-pagination">
          <button 
            className="pagination-btn"
            onClick={handlePrevious}
            disabled={Number(pagination.page) <= 1}
          >
            Previous
          </button>
          <span className="pagination-info">
            Page {pagination.page} of {totalPages} ({pagination.total} total)
          </span>
          <button 
            className="pagination-btn"
            onClick={handleNext}
            disabled={Number(pagination.page) >= totalPages}
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
};

// Helper function to format column names (convert snake_case to Title Case)
const formatColumnName = (columnName) => {
  return columnName
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

// Helper function to format cell values
const formatCellValue = (value) => {
  if (value === null || value === undefined) {
    return '-';
  }
  if (typeof value === 'object') {
    return JSON.stringify(value);
  }
  const stringValue = String(value);
  // Truncate long values to improve readability
  return stringValue.length > 100 ? stringValue.substring(0, 100) + '...' : stringValue;
};

export default EventTable;
