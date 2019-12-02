@extends('admin.layout.master')

@section('title')
   {{ $moduleTitle }}
@endsection
@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-body">        
        <form id="materialForm" data-toggle="validator" action="{{ route($modulePath.'update', [base64_encode(base64_encode($material->id))]) }}" method="post">
            <input type="hidden" name="_method" value="PUT">
            <div class="box-header with-border">
              <h1 class="box-title">{{ $moduleTitleInfo }}</h1>
              <button class="btn btn-primary pull-right" onclick="window.history.back()">Back</button>
            </div>
            @php
            $balanceStock = 0;
            if(isset($material->hasInMaterials)){
				foreach($material->hasInMaterials as $lot){
					$balanceStock = $balanceStock + $lot->lot_balance;		
				}	
			}
            @endphp
            <div class="form-group col-md-12">
                <label class="theme-blue"> 
                Material Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name"
                    value="{{ $material->name }}" 
                    class="form-control" 
                    required
                    maxlength="150" 
                    data-error="Material Name field is required." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_name"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Material Type 
                    <span class="required">*</span></label>
                <select class="form-control my-select" name="material_type" required="" data-error="Unit field is required.">                    
                    <option value="Raw" @if($material->material_type=="Raw") selected @endif>Raw Material</option>
                    <option value="Packaging" @if($material->material_type=="Packaging") selected @endif>Packaging Material</option>
                    <option value="Consumable" @if($material->material_type=="Consumable") selected @endif>Consumable Material</option>
                 </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_material_type"></li>
                    </ul>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Unit 
                    <span class="required">*</span></label>
                <select class="form-control my-select" name="unit" required="" data-error="Unit field is required.">                    
                    <option value="Kg" @if($material->unit=="Kg") selected @endif>Kg</option>
                    <option value="Litre" @if($material->unit=="Litre") selected @endif>Litre</option>
                    <option value="Nos" @if($material->unit=="Nos") selected @endif>Nos</option>
                 </select>
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_unit"></li>
                    </ul>
                </span>
            </div>
            <div class="form-group col-md-6">
                <label class="theme-blue">Balance Stock</label>
                <input 
                    type="number" 
                    name="balance_stock"
                    value="{{ $balanceStock }}"
                    class="form-control" 
                    disabled
                    maxlength="20"
                    step="any"                     
                >               
            </div>

            <div class="form-group col-md-6">
                <label class="theme-blue">Minimum Order Quantity</label>
                <input 
                    type="number" 
                    name="moq"
                    value="{{ $material->moq }}"
                    class="form-control cls-total-qty"                     
                    step="any"
                    maxlength="20" 
                    data-error="Minimum Order Quantity should be number." 
                >
                <span class="help-block with-errors">
                    <ul class="list-unstyled">
                        <li class="err_moq"></li>
                    </ul>
                </span>
            </div>                    
            <div class="form-group col-md-6">
                <label class="theme-blue">Status</label>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" value="1" @if($material->status==1) checked @endif>
                      Active
                    </label>
                </div>  
            </div>
            <div class="box-footer">
                <div class="col-md-12 align-right">
                    <!-- <button type="reset" class="btn btn-danger">Reset</button> -->
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</section>

@endsection
@section('scripts')
    <script type="text/javascript" src="{{ url('assets/admin/js/materials/create-edit.js') }}"></script>    
@endsection