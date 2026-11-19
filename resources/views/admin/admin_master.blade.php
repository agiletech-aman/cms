<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin Dashboard" name="description" />
    <meta content="Agiletech" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <!-- jquery.vectormap css -->
    <link href="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}"
        rel="stylesheet" type="text/css" />
    <!-- Datapicker -->
    <link href="{{ asset('backend/assets/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="{{ asset('backend/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('backend/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Toastr Css-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

</head>

<body data-topbar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">


        @include('admin.body.header')

        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.body.sidebar')
        <!-- Left Sidebar End -->



        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            @yield('admin')
            <!-- End Page-content -->

            @include('admin.body.footer')

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>


    <!-- Datepicker init js -->
    <script src="{{ asset('backend/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- jquery.vectormap map -->
    <script
        src="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script
        src="{{ asset('backend/assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script
        src="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>


    <!-- Toastr js -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if(Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}"
            switch (type) {
                case 'info':
                    toastr.info(" {{ Session::get('message') }} ");
                    break;

                case 'success':
                    toastr.success(" {{ Session::get('message') }} ");
                    break;

                case 'warning':
                    toastr.warning(" {{ Session::get('message') }} ");
                    break;

                case 'error':
                    toastr.error(" {{ Session::get('message') }} ");
                    break;
            }
        @endif
    </script>
    <!--tinymce js-->
    <script src="{{ asset('backend/assets/libs/tinymce/tinymce.min.js') }}"></script>

    <!-- init js -->
    <script src="{{ asset('backend/assets/js/pages/form-editor.init.js') }}"></script>

    <!-- Datatable init js -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}"></script>

    @if(request()->routeIs('dashboard'))
    <script src="https://code.highcharts.com/highcharts.src.js"></script>
    <script>
        // Make AJAX request to get Region-wise histogram data

        ////Region wise new complaint
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('complaint.regionwise') }}", // Adjust the route to match the region-wise endpoint
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!document.getElementById('regionwise-histogram-container')) return;
                    Highcharts.chart('regionwise-histogram-container', {
                        chart: {
                            type: 'column' // Use 'column' for vertical bars
                        },
                        title: {
                            text: data.title.text // Set the chart title
                        },
                        xAxis: {
                            categories: data.xaxis.categories, // Use the states from the data as categories
                            title: {
                                text: data.xaxis.title.text // Set the x-axis title
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: data.yaxis.title.text // Set the y-axis title
                            }
                        },

                        tooltip: {
                            valueSuffix: ' complaints'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0,
                                stacking: 'normal' // Stack bars for different statuses
                            }
                        },
                        series: data.series // Use the series data from the AJAX response
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error); // Log errors if any
                }
            });
        });

        /////Region wise Old complaint data

        $(document).ready(function () {
            $.ajax({
                url: "{{ route('old.complaint.regionwise') }}", // Adjust the route to match the state-wise endpoint
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!document.getElementById('regionwise-histogram-container-old')) return;
                    Highcharts.chart('regionwise-histogram-container-old', {
                        chart: {
                            type: 'column' // Use 'column' for vertical bars
                        },
                        title: {
                            text: data.title.text // Set the chart title
                        },
                        xAxis: {
                            categories: data.xaxis.categories, // Use the states from the data as categories
                            title: {
                                text: data.xaxis.title.text // Set the x-axis title
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: data.yaxis.title.text // Set the y-axis title
                            }
                        },

                        tooltip: {
                            valueSuffix: ' complaints'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0,
                                stacking: 'normal' // Stack bars for different statuses
                            }
                        },
                        series: data.series // Use the series data from the AJAX response
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error); // Log errors if any
                }
            });
        });

        /////WAREHOUSE WISE NEW COMPLAINT
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('complaint.warehousewise') }}", // Adjust the route to match the state-wise endpoint
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!document.getElementById('warehousewise-histogram-container')) return;
                    Highcharts.chart('warehousewise-histogram-container', {
                        chart: {
                            type: 'column' // Use 'column' for vertical bars
                        },
                        title: {
                            text: data.title.text // Set the chart title
                        },
                        xAxis: {
                            categories: data.xaxis.categories, // Use the states from the data as categories
                            title: {
                                text: data.xaxis.title.text // Set the x-axis title
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: data.yaxis.title.text // Set the y-axis title
                            }
                        },
                        tooltip: {
                            valueSuffix: ' complaints'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0,
                                stacking: 'normal' // Stack bars for different statuses
                            }
                        },
                        series: data.series // Use the series data from the AJAX response
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error); // Log errors if any
                }
            });
        });


/////WAREHOUSE WISE OLD COMPLAINT
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('old.complaint.warehousewise') }}", // Adjust the route to match the state-wise endpoint
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!document.getElementById('warehousewise-histogram-container-old')) return;
                    Highcharts.chart('warehousewise-histogram-container-old', {
                        chart: {
                            type: 'column' // Use 'column' for vertical bars
                        },
                        title: {
                            text: data.title.text // Set the chart title
                        },
                        xAxis: {
                            categories: data.xaxis.categories, // Use the states from the data as categories
                            title: {
                                text: data.xaxis.title.text // Set the x-axis title
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: data.yaxis.title.text // Set the y-axis title
                            }
                        },
                        tooltip: {
                            valueSuffix: ' complaints'
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0,
                                stacking: 'normal' // Stack bars for different statuses
                            }
                        },
                        series: data.series // Use the series data from the AJAX response
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error); // Log errors if any
                }
            });
        });




        // Make AJAX request to get chart data
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('complaint.piechart') }}",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!document.getElementById('chart-container')) return;
                    Highcharts.chart('chart-container', {
                        chart: {
                            type: 'pie'
                        },
                        title: {
                            text: data.title.text
                        },
                        series: [{
                            name: data.series[0].name,
                            data: data.series[0].data,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }]
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        });

        //old complaint pie chart
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('oldcomplaint.piechart') }}",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!document.getElementById('chart-container-oldcomplaint')) return;
                    Highcharts.chart('chart-container-oldcomplaint', {
                        chart: {
                            type: 'pie'
                        },
                        title: {
                            text: data.title.text
                        },
                        series: [{
                            name: data.series[0].name,
                            data: data.series[0].data,
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }]
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        });




        $(document).ready(function () {
            $("form[name='uptime']").on("submit", function (event) {
                event.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('complaint.uptime') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (data) {
                        if (!document.getElementById('uptime-container')) return;
                        Highcharts.chart('uptime-container', {
                            chart: {
                                type: 'pie'
                            },
                            title: {
                                text: 'Uptime Analysis'
                            },
                            series: [{
                                name: 'Uptime/Downtime',
                                data: data.series[0].data,
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                }
                            }]
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>

   

        

<script>
    // MONTLY NEW COMPLAINTS
$(document).ready(function () {

    function loadMonthlyChart(year) {
        $.ajax({
            url: "{{ route('complaint.histogram') }}", // 👈 your route
            type: "GET",
            dataType: "json",
            data: { year: year },
            success: function (data) {
                if (!document.getElementById('histogram-container')) return;
                Highcharts.chart('histogram-container', {
                    chart: { type: 'column' },
                    title: { text: data.title.text },
                    xAxis: {
                        categories: data.xaxis.categories,
                        title: { text: data.xaxis.title.text }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: data.yaxis.title.text }
                    },
                    tooltip: { valueSuffix: ' complaints' },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            stacking: 'normal'
                        }
                    },
                    series: data.series
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // ✅ initial load
    loadMonthlyChart($('#complaint-year-new').val());

    // ✅ reload on dropdown change
    $('#complaint-year-new').on('change', function () {
        loadMonthlyChart($(this).val());
    });

});

</script>


<script>
    // Monthly Old 
$(document).ready(function () {

function loadMonthlyChartOld(year) {

    if (!document.getElementById('histogram-container-old')) {
        return; // ⛔ container not on page
    }
            $.ajax({
            url: "{{ route('old.complaint.histogram') }}", // 👈 your route
            type: "GET",
            dataType: "json",
            data: { year: year },
            success: function (data) {
                Highcharts.chart('histogram-container-old', {
    chart: { type: 'column' },
    title: { text: data.title.text },
    xAxis: {
        categories: data.xaxis.categories,
        title: { text: 'Month' }
    },
    yAxis: {
        min: 0,
        title: { text: data.yaxis.title.text }
    },
    tooltip: { valueSuffix: ' complaints' },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
            stacking: 'normal'
        }
    },
    series: data.series
});

            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // ✅ initial load
    loadMonthlyChartOld($('#complaint-year-old').val());

    // ✅ reload on dropdown change
    $('#complaint-year-old').on('change', function () {
        loadMonthlyChartOld($(this).val());
    });

});
</script>
    @endif

        


    <script>
        $("#datatable").DataTable({
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>",
                },
            },
            drawCallback: function () {
                $(".dataTables_paginate > .pagination").addClass(
                    "pagination-rounded"
                );
            },
        });
    </script>

</body>

</html>
