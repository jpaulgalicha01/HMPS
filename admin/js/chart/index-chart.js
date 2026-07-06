function chartPwdCat() {
  fncExecute("inputConfig.php?getPwdCategory=true", null, function (response, textStatus, jqXHR) {
    var response = JSON.parse(response);
    if (response.status == 200) {
      var result = response.data;

      // Map counts into series aligned with your fixed labels
      var categories = [
        'N/A', 'Psychosocial Disability', 'Chronic Illness', 'Learning Disability',
        'Visual Disability', 'Orthopedic / Physical Disability',
        'Mental Disability / Intellectual Disability',
        'Hearing Disability (Deaf / Hard of Hearing)',
        'Speech and Language Impairment',
        'Cancer and Rare Diseases',
        'Others'
      ];
          // Define fixed colors per category
      var categoryColors = [
        '#368bf3', // N/A
        '#ff6384', // Psychosocial Disability
        '#36a2eb', // Chronic Illness
        '#ffcd56', // Learning Disability
        '#4bc0c0', // Visual Disability
        '#9966ff', // Orthopedic / Physical Disability
        '#c9cbcf', // Mental Disability / Intellectual Disability
        '#ff9f40', // Hearing Disability
        '#00a65a', // Speech and Language Impairment
        '#e83e8c', // Cancer and Rare Diseases
        '#6c757d'  // Others
      ];

      var seriesData = categories.map(cat => {
        var match = result.find(r => r.type_of_disability === cat);
        return match ? parseInt(match.total_count, 10) : 0;
      });

     var options = {
        series: seriesData,
        chart: {
          width: '100%',
          height: 300,
          type: 'pie',
        },
        labels: categories,
        colors: categoryColors,
        legend: {
          position: 'right',
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: { width: 200 },
              legend: { position: 'bottom' },
            },
          },
        ],
      };

      var chart = new ApexCharts(document.querySelector('#chart'), options);
      chart.render();
    }
  }, "GET");
}




function chartAgeCat (){

  var options = {
  series: [
    {
      name: 'TOTAL COUNT',
      data: [44, 55, 41, 67, 22, 43, 21,],
    },
  ],
  chart: {
    height: 350,
    type: 'bar',
  },
  plotOptions: {
    bar: {
      borderRadius: 10,
      columnWidth: '50%',
    },
  },
  dataLabels: {
    enabled: true,
  },
  stroke: {
    width: 0,
  },
  grid: {
    row: {
      colors: ['#fff', '#f2f2f2'],
    },
  },
  xaxis: {
    labels: {
      rotate: -40,
    },
    categories: [
      'Infant / Toddler (0 to 4 years old)',
      'Child (5 to 9 years old)',
      'Adolescent (10 to 19 years old)',
      'Teenager (13 to 19 years old)',
      'Youth (15 to 30 years old)',
      'Working-Age / Adult (15 to 64)',
      'Senior Citizen / Elderly (60 years old and above)',
    ],
    tickPlacement: 'on',
  },
 
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: 'horizontal',
      shadeIntensity: 0.25,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 0.85,
      opacityTo: 0.85,
      stops: [50, 0, 100],
    },
  },
}

var chart = new ApexCharts(document.querySelector('#chartAgeCat'), options)
chart.render()
}