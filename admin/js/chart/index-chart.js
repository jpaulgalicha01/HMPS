function chartPwdCat(){

  
var options = {
  series: [44, 55, 13, 43, 22],
  chart: {
    // make width/height dynamic based on the #chart container
    width: '100%',
    height: 300,
    type: 'pie',
  },
  labels: [
    'N/A', 'Psychosocial Disability', 'Chronic Illness', 'Learning Disability', 'Visual Disability','Orthopedic / Physical Disability','Mental Disability / Intellectual Disability',
    'Hearing Disability (Deaf / Hard of Hearing)','Speech and Language Impairment','Cancer and Rare Diseases','Others'

  ],
  legend: {
    position: 'right',
  },

  responsive: [
    {
      breakpoint: 480,
      options: {
        chart: {
          width: 200,
        },
        legend: {
          position: 'bottom',
        },
      },
    },
  ],
}

var chart = new ApexCharts(document.querySelector('#chart'), options)
chart.render()

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