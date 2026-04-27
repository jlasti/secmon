import React, { useMemo, useState, useEffect } from 'react';
import { ComposableMap, Geographies, Geography } from 'react-simple-maps';
import './GeoMap.css';

const geoUrl = '/data/countries-110m.json';
let cachedGeographies = null;
let geographiesLoadingPromise = null;

const loadGeographies = () => {
  if (cachedGeographies) {
    return Promise.resolve(cachedGeographies);
  }
  if (geographiesLoadingPromise) {
    return geographiesLoadingPromise;
  }
  geographiesLoadingPromise = fetch(geoUrl)
    .then(res => res.json())
    .then(data => {
      cachedGeographies = data;
      geographiesLoadingPromise = null;
      return data;
    })
    .catch(err => {
      geographiesLoadingPromise = null;
      throw err;
    });
  return geographiesLoadingPromise;
};

export default function GeoMap({ data }) {
  const [hoveredCountry, setHoveredCountry] = useState(null);
  const [geographiesData, setGeographiesData] = useState(null);

  // Load geographies once and cache them
  useEffect(() => {
    loadGeographies()
      .then(data => setGeographiesData(data))
      .catch(err => console.error('Error loading geographies:', err));
  }, []);

  // Organize data by country code and build reverse map from names
  const eventsByCode = useMemo(() => {
    const map = {};
    if (data && Array.isArray(data)) {
      data.forEach(item => {
        const code = item.code || 'UNKNOWN';
        map[code] = item.count || 0;
      });
    }
    return map;
  }, [data]);

  // Map country names from data to their codes for dynamic lookup
  const countryNameToCode = useMemo(() => {
    const map = {};
    if (data && Array.isArray(data)) {
      data.forEach(item => {
        if (item.country && item.code) {
          map[item.country] = item.code;
        }
      });
    }
    return map;
  }, [data]);

  // Calculate stats
  const stats = useMemo(() => {
    const events = Object.values(eventsByCode).reduce((sum, count) => sum + count, 0);
    const maxCount = Math.max(...Object.values(eventsByCode), 0);
    return { events, maxCount };
  }, [eventsByCode]);

  // Color function based on event count
  const getColor = (count) => {
    if (!count || count === 0) return '#f0f0f0';
    const intensity = Math.log(count + 1) / Math.log(stats.maxCount + 1);
    return intensity > 0.8 ? '#800026' :
           intensity > 0.6 ? '#BD0026' :
           intensity > 0.4 ? '#E31A1C' :
           intensity > 0.2 ? '#FC4E2A' :
           intensity > 0.1 ? '#FD8D3C' :
           '#FEB24C';
  };

  // Exceptions: geojson country names that don't match data country names
  const countryCodeExceptions = {
    'United States of America': 'US',
    'United Kingdom': 'GB',
    // Add more exceptions as needed
  };

  const getCountryCode = (geojsonName) => {
    // First check if this is an exception
    if (countryCodeExceptions[geojsonName]) {
      return countryCodeExceptions[geojsonName];
    }
    // Then try to match against country names in data
    return countryNameToCode[geojsonName] || null;
  };

  return (
    <div className="geomap-container">
      <div className="geomap-map">
        {!geographiesData ? (
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%', color: '#999' }}>
            Loading map...
          </div>
        ) : (
          <ComposableMap
            className="geomap-svg"
            style={{ width: '100%', height: '100%' }}
            preserveAspectRatio="xMidYMid meet"
          >
            <Geographies geography={geographiesData}>
              {({ geographies }) =>
                geographies.map((geo) => {
                  const countryCode = getCountryCode(geo.properties.name);
                  const eventCount = eventsByCode[countryCode] || 0;
                  const fillColor = getColor(eventCount);

                  return (
                    <Geography
                      key={geo.rsmKey}
                      geography={geo}
                      onMouseEnter={() => setHoveredCountry({ name: geo.properties.name, code: countryCode })}
                      onMouseLeave={() => setHoveredCountry(null)}
                      style={{
                        default: {
                          fill: fillColor,
                          stroke: '#fff',
                          strokeWidth: 0.75,
                          outline: 'none',
                          cursor: eventCount > 0 ? 'pointer' : 'default',
                          transition: 'all 250ms',
                        },
                        hover: {
                          fill: fillColor,
                          stroke: '#333',
                          strokeWidth: 1,
                          outline: 'none',
                          cursor: eventCount > 0 ? 'pointer' : 'default',
                          filter: 'brightness(0.9)',
                          transition: 'all 250ms',
                        },
                        pressed: {
                          fill: fillColor,
                          stroke: '#333',
                          strokeWidth: 1.5,
                          outline: 'none',
                        },
                      }}
                    />
                  );
                })
              }
            </Geographies>
          </ComposableMap>
        )}
      </div>

      {hoveredCountry && (
        <div className="geomap-tooltip">
          <strong>{hoveredCountry.name}</strong>
          <br />
          Events: {eventsByCode[hoveredCountry.code] || 0}
        </div>
      )}
    </div>
  );
}
