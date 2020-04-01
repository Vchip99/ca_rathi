<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="{{ asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{ asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/sidemenu_layout.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/sidemenu/_all-skins.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>

  <script src="{{ asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{ asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
  <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  </script>
  <style type="text/css">
  .admin_table{
    padding-top: 10px;
    background-color: #01bafd;
  }
  .admin_div{
    padding: 10px;
    background-color: #01bafd;
  }
  @media only screen and (max-width: 767px){
    select,input[type=text]{
      margin-bottom: 5px;
    }
  }
  </style>
  @yield('dashboard_header')
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">
  <header class="main-header">
    <a href="" class="logo">
      <span class="logo-mini"><b> RCF</b></span>
      <span class="logo-lg"><b>RCF</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
        </ul>
      </div>
    </nav>
  </header>
  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
        <a href="{{ url('admin/home') }}">
          <img src="{{ asset('images/user1.png')}}" class="img-circle" alt="User Image">
        </a>
        </div>
        <div class="pull-left info">
          @php
            $adminUser = Auth::guard('admin')->user();
            $superSuperAdmin = 1;
            $superAdmin = 2;
            $admin = 3;
            $subAdmin = 4;
          @endphp
          <p>{{ucfirst($adminUser->name)}}</p>
          <a><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li class="header">Admin</li>
        @if($subAdmin == $adminUser->type || $superAdmin == $adminUser->type || $superSuperAdmin == $adminUser->type)
        <li class="treeview ">
          <a href="#" title="Student Admission">
            <i class="fa fa-dashboard"></i> <span>Student Admission</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if($subAdmin == $adminUser->type || $superAdmin == $adminUser->type)
              <li title="Admission Receipt"><a href="{{ url('admin/create-admission-receipt')}}"><i class="fa fa-circle-o"></i>Admission Receipt</a></li>
            @endif
            @if($subAdmin == $adminUser->type || $superAdmin == $adminUser->type || $superSuperAdmin == $adminUser->type)
              <li title="Course Payments"><a href="{{ url('admin/manage-course-payment')}}"><i class="fa fa-circle-o"></i>Course Payments</a></li>
            @endif
            @if($subAdmin == $adminUser->type || $superAdmin == $adminUser->type)
              <li title="Refund Formation"><a href="{{ url('admin/manage-refund')}}"><i class="fa fa-circle-o"></i>Refund Formation</a></li>
            @endif
            @if($superSuperAdmin == $adminUser->type || $superAdmin == $adminUser->type)
              <li title="Outstanding"><a href="{{ url('admin/outstanding')}}"><i class="fa fa-circle-o"></i>Outstanding</a></li>
            @endif
            @if($subAdmin == $adminUser->type || $superAdmin == $adminUser->type)
              <li title="Enquiry"><a href="{{ url('admin/enquiries')}}"><i class="fa fa-circle-o"></i>Enquiry</a></li>
              <li title="User Courses"><a href="{{ url('admin/userCourses')}}"><i class="fa fa-circle-o"></i>User Courses</a></li>
            @endif
          </ul>
        </li>
        @endif
        @if($superAdmin == $adminUser->type)
        <li class="treeview ">
          <a href="#" title="CRUD Formation">
            <i class="fa fa-university"></i> <span>CRUD Formation</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Batch Formation"><a href="{{ url('admin/manage-batch')}}"><i class="fa fa-circle-o"></i>Batch Formation</a></li>
            <li title="Sub Course Formation"><a href="{{ url('admin/manage-subcourse')}}"><i class="fa fa-circle-o"></i>Sub Course Formation</a></li>
            <li title="Course Formation"><a href="{{ url('admin/manage-course')}}"><i class="fa fa-circle-o"></i>Course Formation</a></li>
          </ul>
        </li>
        @endif
        @if($admin == $adminUser->type || $superAdmin == $adminUser->type)
        <li class="treeview ">
          <a href="#" title="Discount">
            <i class="fa fa-diamond"></i> <span>Discount</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Discount Formation"><a href="{{ url('admin/manage-discount')}}"><i class="fa fa-circle-o"></i>Discount Formation</a></li>

          </ul>
        </li>
        @endif
        <li class="treeview ">
          <a href="#" title="Admin">
            <i class="fa fa-user"></i> <span>Admin</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if($superAdmin == $adminUser->type)
              <li title="Admin Formation"><a href="{{ url('admin/manage-admin')}}"><i class="fa fa-circle-o"></i>Admin Formation</a></li>
            @endif
            <li title="Update Password"><a href="{{ url('admin/manage-password')}}"><i class="fa fa-circle-o"></i>Update Password</a></li>
          </ul>
        </li>
        @if($superAdmin == $adminUser->type)
        <li class="treeview ">
          <a href="#" title="Delete">
            <i class="fa fa-trash"></i> <span>Delete</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li title="Delete Payments"><a href="{{ url('admin/delete-payments')}}"><i class="fa fa-circle-o"></i>Delete Payments</a></li>
          </ul>
        </li>
        @endif
        <li class="header">LABELS</li>
        <li>
          <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
            <i class="fa fa-sign-out" aria-hidden="true"></i><span>Logout {{ucfirst($adminUser->name)}}</span>
            <span class="pull-right-container"></span>
          </a>
          <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
        </li>
      </ul>
    </section>
  </aside>
  <div class="content-wrapper">
    @yield('module_title')
    <div class="content">
      <div class="row">
        @yield('admin_content')
      </div>
    </div>
  </div>
<script type="text/javascript">
  $(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 10000); // <-- time in milliseconds
    });
</script>
</body>
</html>
