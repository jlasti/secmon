import React from 'react';
import {
  PieChart as RechartsPieChart,
  Pie,
  Cell,
  Tooltip,
  Legend
} from 'recharts';

const COLORS = [
  '#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8',
  '#82CA9D', '#FFC658', '#FF6B9D', '#C2185B', '#7B1FA2',
  '#512DA8', '#303F9F', '#1976D2', '#0288D1', '#0097A7'
];

const PieChart = ({ data, config }) => {
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
    name: item.label || item.x || 'Unknown',
    value: item.count || item.y || 0
  }));
  const selectedVariable = config?.pie_chart_variable || 'value';

  const animationConfig = {
    isAnimationActive: true,
    animationDuration: 550,
    animationEasing: 'ease-out'
  };

  const CustomTooltip = ({ active, payload }) => {
    if (active && payload && payload.length) {
      const data = payload[0];
      const total = chartData.reduce((sum, item) => sum + item.value, 0);
      const percentage = ((data.value / total) * 100).toFixed(2);
      
      return (
        <div style={{
          backgroundColor: 'rgba(255, 255, 255, 0.95)',
          border: '1px solid #ccc',
          borderRadius: '4px',
          padding: '10px'
        }}>
          <p style={{ margin: '0 0 5px 0', fontWeight: 'bold' }}>
            {data.name}
          </p>
          <p style={{ margin: '0', color: '#666' }}>
            Počet: {data.value}
          </p>
          <p style={{ margin: '0', color: '#666' }}>
            {percentage}%
          </p>
        </div>
      );
    }
    return null;
  };

  return (
    <RechartsPieChart 
      responsive 
      style={{ 
        height: '100%', 
        width: '100%',
        minHeight: '250px'
      }}
    >
      <Pie
        data={chartData}
        cx="50%"
        cy="50%"
        labelLine={false}
        outerRadius="80%"
        innerRadius={config.innerRadius || "0%"}
        fill="#8884d8"
        dataKey="value"
        {...animationConfig}
      >
        {chartData.map((entry, index) => (
          <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
        ))}
      </Pie>
      <Tooltip content={<CustomTooltip />} />
      {config.show_labels && <Legend 
          verticalAlign="bottom" 
          height={36}
          wrapperStyle={{ fontSize: '12px' }}
          formatter={(value) => `${selectedVariable}: ${value}`}
        />}
    </RechartsPieChart>
  );
};

export default PieChart;
