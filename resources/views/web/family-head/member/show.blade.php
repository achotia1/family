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
            <h2 class="box-title">Family Member Details</h2>
        </div>
        <div class="box-body">
            <table id="listingTable" class="table table-bordered table-striped" style="width:100%" >
                <thead class="blue-border-bottom">
                    <tr>
                        <th>Name</th>
                        <th>Birth Date</th>
                        <th>Education</th>
                        <th>Martial Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if( !empty($familyHeadHasMembers) && count($familyHeadHasMembers) > 0 )
                    @foreach($familyHeadHasMembers as $familyHeadHasMember)
                        @if( !empty($familyHeadHasMember->associatedFamilyMembers) )
                        <tr>
                            <td>
                                {{ $familyHeadHasMember->associatedFamilyMembers->first_name .' '. $familyHeadHasMember->associatedFamilyMembers->last_name }}
                            </td>
                            <td>
                                {{ date('Y-m-d',strtotime($familyHeadHasMember->associatedFamilyMembers->birth_date)) }}
                            </td>
                            <td>
                                {{ $familyHeadHasMember->associatedFamilyMembers->education }}
                            </td>
                            <td>
                                {{ $familyHeadHasMember->associatedFamilyMembers->martial_status == 1 ? 'Married' : 'Unmarried' }}
                            </td>
                        </tr>
                         @endif
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