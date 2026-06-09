import React from 'react';
import {
  LineChart as RechartsLineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
} from 'recharts';

const LineChart = ({ data }) => {
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

  const animationConfig = {
    isAnimationActive: true,
    animationDuration: 500,
    animationEasing: 'ease-out'
  };

  return (
    <RechartsLineChart
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
        interval="preserveStartEnd"
        tick={{ fontSize: 12 }}
      />
      <YAxis
        label={{ value: 'Count', angle: -90, position: 'insideLeft' }}
        tick={{ fontSize: 12 }}
      />
      <Tooltip />
      <Line
        type="monotone"
        dataKey="value"
        stroke="#8884d8"
        strokeWidth={2}
        dot={{ r: 4 }}
        activeDot={{ r: 6 }}
        name="Count"
        {...animationConfig}
      />
    </RechartsLineChart>
  );
};

export default LineChart;
