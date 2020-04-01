<!DOCTYPE html>
<html lang="en">
  	<head>
      	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      	<meta http-equiv="X-UA-Compatible" content="IE=edge">
      	<meta name="viewport" content="width=device-width, initial-scale=1">
      	<link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
	    <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
	    <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  	</head>
	<body>
	<div class="container">
		<div class="" style="border: solid;border-color: black;" >
			<div class="form-group" style="">
				<div class="col-sm-12" align="right" style="border-bottom: 1px solid;">Tax Invoice&nbsp;&nbsp;</div>
			</div>
			<div class="form-group row" style="">
				<div class="" style="padding-left: 50px;">Receipt By: {{$courseReceiptArr['receipt_by']}}
				<span style="float: right; padding-right: 20px;">Receipt No:{{$courseReceiptArr['receipt_id']}}</span></div>
			</div>
	       	<div class="form-group row" style="">
	            <div class="col-sm-7">&nbsp;&nbsp;
	       			<strong>Address:</strong>
					Rathi Career Forum,Amaravati
				</div>
				<div class="col-sm-5"  align="right">Date:{{$courseReceiptArr['date']}}&nbsp;&nbsp;</div>
	        </div>
	        <hr style="bottom: solid;color: black;">
	        <div class="form-group row" style="">
	            <div class="col-sm-4" >
	            GSTIN:
	            @if(!empty($courseReceiptArr['gstin']))
	            	{{$courseReceiptArr['gstin']}}
	            @endif
	            </div>
	            <div  class="col-sm-4" >
	            CIN:
	            @if(!empty($courseReceiptArr['cin']))
	            	{{$courseReceiptArr['cin']}}
	            @endif
	            </div>
	            <div  class="col-sm-4" >
	            PAN:
	            @if(!empty($courseReceiptArr['pan']))
	            	{{$courseReceiptArr['pan']}}
	            @endif
	            </div>
	        </div>
	        <hr style="bottom: solid;color: black;">
	        <div class="form-group  row">
	            <div class="col-sm-12 ">&nbsp;&nbsp;<label>Billed To:</label> {{$courseReceiptArr['f_name']}} {{$courseReceiptArr['m_name']}} {{$courseReceiptArr['l_name']}}</div>
	            <div class="col-sm-12">&nbsp;&nbsp;<label>User Id:</label> {{$courseReceiptArr['user_id']}}</div>
	            <div class="col-sm-12">&nbsp;&nbsp;<label>State Code:</label> 27</div>
	            <div class="col-sm-12">&nbsp;&nbsp;<label>Service:</label> Coaching for {{$courseReceiptArr['subcourse']}}</div>
	        </div>
	        <hr style="bottom: solid;color: black;">
	        <div class="form-group row">
	            <div class="col-sm-12">&nbsp;&nbsp;<label>Payment:</label></div>
	            @if(1 == $courseReceiptArr['fee_type'])
		            <div class="col-sm-12">&nbsp;&nbsp;Sub Total:{{ round($courseReceiptArr['amount']/1.18,2)}},&nbsp;&nbsp;CGST: {{round((  $courseReceiptArr['amount']/1.18) * 0.09,2)}}, &nbsp;&nbsp;SGST:{{round(($courseReceiptArr['amount']/1.18) * 0.09,2)}} </div>
		            <div class="col-sm-12">&nbsp;&nbsp;<label>Total:</label> Rs. {{$courseReceiptArr['amount']}} </div>
		        @else
		        	<div class="col-sm-12">&nbsp;&nbsp;Sub Total:{{ $courseReceiptArr['amount']}},&nbsp;&nbsp;CGST: 0 , &nbsp;&nbsp;SGST:0  </div>
		            <div class="col-sm-12">&nbsp;&nbsp;<label>Total:</label> Rs. {{$courseReceiptArr['amount']}} </div>
		        @endif
	        </div>
	        <hr style="bottom: solid;color: black;">
	        <div class="form-group row">
	            <div class="col-sm-6">&nbsp;&nbsp;Customer Signature</div>
	            <div class="col-sm-6" align="right">Authorised Signature&nbsp;&nbsp;</div>
	        </div>
    	</div>
    </div>
	</body>
</html>