@extends('web.layout.main')

@section('title')
{{ $moduleTitle ?? 'Manage Family' }}
@endsection

@section('styles')
@endsection

@section('content')
<section class="content">
    <div class="box">
        <div class="box-header align-right">
            <h2 class="box-title">Family Head List</h2>
            <a href="{{ route($modulePath.'.member.create') }}" class="btn btn-primary pull-right" style="margin: 2px;"><i class="fa fa-plus"></i> Add Family Members</a>

            <a href="{{ route($modulePath.'.create') }}" class="btn btn-primary pull-right" style="margin: 2px;"><i class="fa fa-plus"></i> Add Family Head</a>

        </div>
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th>Name</th>
                        <th>Birth Date</th>
                        <th>Mobile</th>
                        <th>Hobbies</th>
                        <th>No. of Family Members</th>
                    </tr>
                </thead>
                <tbody>
                    @if( !empty($familyHeads) && count($familyHeads) > 0 )
                    @foreach($familyHeads as $familyHead)
                        <tr>
                            <td>
                                {{ $familyHead->first_name .' '. $familyHead->last_name }}
                            </td>
                            <td>
                                {{ date('Y-m-d',strtotime($familyHead->birth_date)) }}
                            </td>
                            <td>
                                {{ $familyHead->mobile_number }}
                            </td>
                            <td>
                                {{ $familyHead->hobbies }}
                            </td>
                            <td>{!! $familyHead->no_of_family_members !!}</td>
                        </tr>
                    @endforeach
                    @else
                     <th colspan="6" class="text-center">No Records Found.</th>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>
   
@endsection