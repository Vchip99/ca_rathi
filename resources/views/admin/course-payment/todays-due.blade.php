@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Todays Dues </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Student Admission </li>
      <li class="active"> Todays Dues </li>
    </ol>
  </section>
  <style type="text/css">
    .table > thead > tr > th,.table > tbody > tr > td,.table > tbody > tr > th {
        border-bottom: 2px solid black !important;
    }
  </style>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
    <div style="overflow: auto;">
      <table class="table" border="1">
        <thead class="">
          <tr>
            <th>#</th>
            <th>UserId</th>
            <th>Phone</th>
            <th>Course</th>
            <th>Sub Course</th>
            <th>Batch</th>
            <th>Total Fee</th>
            <th>Paid</th>
            <th>Discount</th>
            <th>Outstanding</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $index = 1;
          @endphp
          @if(count($usersOutstanding) > 0)
            @foreach($usersOutstanding as $userOutstanding)
              <tr>
                <td>{{ $index++ }}</td>
                <td>{{$userOutstanding['user_id']}}</td>
                <td>{{$userOutstanding['phone']}}</td>
                <td>{{($userOutstanding['course'])?:deleted}}</td>
                <td>{{($userOutstanding['subcourse'])?:deleted}}</td>
                <td>{{($batchesName[$userOutstanding['batch']])?:deleted}}</td>
                <td>{{$userOutstanding['total_fee']}}</td>
                <td>{{$userOutstanding['paid']}}</td>
                <td>{{$userOutstanding['discount']}}</td>
                <td>{{$userOutstanding['outstanding']}}</td>
              </tr>
            @endforeach
            <tr>
                <td colspan="7">&nbsp;</td>
                <td colspan="2">Total Outstanding:</td>
                <td>{{ $totalOutstanding }}</td>
            </tr>
          @else
            <tr>
              <td colspan="10">No Dues for today</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
</div>
@stop