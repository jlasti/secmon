import React from 'react';
import {
  BarChart as RechartsBarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
} from 'recharts';

const BarChart = ({ data, config = {} }) => {
  if (!data || data.length === 0) {
    return (
      <div style={{ 
        display: 'flex', 
        alignItems: 'center', 
        justifyContent: 'center', 
        height: '100%',
        color: '#999'
      }}>
        No data available
      </div>
    );
  }

  const chartData = data.map(item => ({
    name: item.x || item.label || 'Unknown',
    value: item.y || item.count || 0
  }));
  const selectedVariable = config.bar_chart_variable || 'value';
  const legendLabel = `Count (${selectedVariable})`;

  const animationConfig = {
    isAnimationActive: true,
    animationDuration: 450,
    animationEasing: 'ease-out'
  };

  return (
    <RechartsBarChart
      responsive
      style={{
        width: '100%',
        height: '100%',
        minHeight: '250px'
      }}
      data={chartData}
      margin={{ top: 20, right: 30, left: 20, bottom: 60 }}
    >
      <CartesianGrid strokeDasharray="3 3" stroke="#e0e0e0" />
      <XAxis
        dataKey="name"
        angle={-45}
        textAnchor="end"
        height={80}
        interval={0}
        tick={{ fontSize: 12 }}
      />
      <YAxis
        label={{ value: 'Count', angle: -90, position: 'insideLeft' }}
        tick={{ fontSize: 12 }}
      />
      <Tooltip
        contentStyle={{
          backgroundColor: 'rgba(255, 255, 255, 0.95)',
          border: '1px solid #ccc',
          borderRadius: '4px'
        }}
      />
      <Legend />
      <Bar
        dataKey="value"
        fill="#039be5"
        name={legendLabel}
        radius={[8, 8, 0, 0]}
        {...animationConfig}
      />
    </RechartsBarChart>
  );
};

export default BarChart;
