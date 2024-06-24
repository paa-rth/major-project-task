document.addEventListener('DOMContentLoaded', function() {
  const stateCodeDropdown = document.getElementById('state_code');
  const districtCodeDropdown = document.getElementById('district_code');

  const districtCodes = {
      'MH': [
          { code: '01', name: 'Mumbai (South)' },
          { code: '02', name: 'Mumbai (West)' },
          { code: '03', name: 'Mumbai (East)' },
          { code: '04', name: 'Thane' },
          { code: '05', name: 'Kalyan' }
          // Add other district codes for MH
      ],
      'DL': [
          { code: '01', name: 'Central Delhi' },
          { code: '02', name: 'North Delhi' },
          { code: '03', name: 'South Delhi' },
          { code: '04', name: 'East Delhi' }
          // Add other district codes for DL
      ],
      // Add other states and their district codes here
  };

  stateCodeDropdown.addEventListener('change', function() {
      const selectedState = stateCodeDropdown.value;

      // Clear the district code dropdown
      districtCodeDropdown.innerHTML = '<option value="">Select District</option>';

      if (selectedState) {
          const districts = districtCodes[selectedState];
          if (districts) {
              districts.forEach(function(district) {
                  const option = document.createElement('option');
                  option.value = district.code;
                  option.textContent = district.name;
                  districtCodeDropdown.appendChild(option);
              });
          }
      }
  });
});
