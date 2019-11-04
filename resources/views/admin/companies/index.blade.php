@extends('admin.layout.master')

@section('title')
{{ $moduleTitle }}
@endsection

@section('styles')
@endsection

@section('content')

<div class="row mb-5">
      <div class="col-xs-12 bundle">
            <div class="card border-0 shadow">
                  <h1 class="title blue-border-bottom">
                        Manage Company
                  </h1>
                  <div class="card-footer d-flex theme-bg-blue-light blue-border-bottom">
                        <a href="{{ route($modulePath) }}" class="blue-btn ml-auto">Add Company</a>
                        <a href="javascript:void(0)" onclick="return deleteCollections(this)"
                              class="blue-btn ml-3">Delete Selected</a>
                  </div>
                  <table id="listingTable" class="table mb-0 first-child-border-0 even-odd-row-1">
                        <thead class="">
                              <tr>
                                    <th style="display: none"></th>
                                    <th class="w-90-px">Select</th>
                                    <th>Company Name</th>
                                    <th class="w-280-px">URL</th>
                                    <th class="w-100-px">Status</th>
                                    <th class="w-180-px">Actions</th>
                              </tr>
                        </thead>
                        <tbody>
                        </tbody>
                  </table>
            </div>
      </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ url('assets/admin/js/companies/index.js') }}"></script>
@endsection