<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\FamilyHeadsModel;
use App\Models\FamilyHeadHasMemberModel;
use App\Models\StatesModel;
use App\Models\CitiesModel;

// Request
use App\Http\Requests\Web\FamilyHeadRequest;
use App\Http\Requests\Web\FamilyMemberRequest;

// plugins
use Hash;
use DB;
use Storage;
use Carbon;

class FamilyHeadController extends Controller
{
    private $BaseModel;

    public function __construct(
        FamilyHeadsModel $FamilyHeadsModel,
        FamilyHeadHasMemberModel $FamilyHeadHasMemberModel,
        StatesModel $StatesModel,
        CitiesModel $CitiesModel
    )
    {

        $this->BaseModel                = $FamilyHeadsModel;
        $this->FamilyHeadHasMemberModel = $FamilyHeadHasMemberModel;
        $this->StatesModel  = $StatesModel;
        $this->CitiesModel  = $CitiesModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Family Head';
        $this->ModuleView  = 'web.family-head.';
        $this->ModulePath  = 'web.head';
    }

    public function index()
    {
        $this->ViewData['moduleTitle']  = 'Manage '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;
       

        $familyHeads = $this->BaseModel
                            ->with('hasFamilyMembers')
                            ->where('member_type',1)
                            ->get();

        $familyHeads = $familyHeads->map(function($item, $key)
                        {
                         $no_of_family_members = count($item->hasFamilyMembers);
                         $item->no_of_family_members = 0;
                         if($no_of_family_members > 0 ){

                            $item->no_of_family_members = '<a target="_blank" href="' . route($this->ModulePath.'.member.show', [base64_encode(base64_encode($item->id))]) . '">' . $no_of_family_members . '</a>';
                         }
                            return $item;
                        });
        $this->ViewData['familyHeads']   = $familyHeads;

        // view file with data
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function create()
    {
        $this->ViewData['moduleTitle']  = $this->ModuleTitle. " Information";
        $this->ViewData['modulePath']   = $this->ModulePath;

        $this->ViewData['states'] = $this->StatesModel->get();
        $this->ViewData['cities'] = $this->CitiesModel->get();

        // view file with data
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(FamilyHeadRequest $request)
    {

        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Fail to create Family Head.';

        $age = self::_calculateAge($request->birth_date);

        if($age <= 21 ) {

            $this->JsonData['msg'] = 'Birth Date is not allowed. Your age must be greater than 21.'; 
            return response()->json($this->JsonData);
        }
        

        try {

            $collection     = new $this->BaseModel;   
            $collection     = self::_storeOrUpdate( $collection, $request );
            if ($collection) 
            {
                
                $this->JsonData['status'] = 'success';
                $this->JsonData['url']    =  route($this->ModulePath.'.index');
                $this->JsonData['msg']    = 'Family head created successfully.';
                    
            }
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function createFamilyMember()
    {
        $this->ViewData['moduleTitle']  = "Family Member Information";
        $this->ViewData['modulePath']   = $this->ModulePath;

         $familyHeads = $this->BaseModel
                            ->where('member_type',1)
                            ->get();

        $this->ViewData['familyHeads']   = $familyHeads;

        // view file with data
        return view($this->ModuleView.'member.create', $this->ViewData);
    }

    public function storeFamilyMember(FamilyMemberRequest $request)
    {
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Fail to create Family member.';

        try {

            $collection     = new $this->BaseModel;   
            $collection     = self::_storeOrUpdate( $collection, $request, 2 );
            if( $collection ) {

                $collectionMember     = new $this->FamilyHeadHasMemberModel;
                $collectionMember->family_head_id  = $request->family_head_id;
                $collectionMember->member_id       = $collection->id;

                if( $collectionMember->save() ) {
                    $this->JsonData['status'] = 'success';
                    $this->JsonData['url']    =  route($this->ModulePath.'.index');
                    $this->JsonData['msg']    = 'Family member created successfully.';
                }
                    
            }
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function showFamilyMemberDetails($encID)
    {
        $id = base64_decode( base64_decode( $encID ) );

        $familyHeadHasMembers = $this->FamilyHeadHasMemberModel
                                ->with('associatedFamilyMembers')
                                ->where('family_head_id',$id)
                                ->get();
        $this->ViewData['moduleTitle']  = 'Family Member Details';
        $this->ViewData['modulePath']   = $this->ModulePath;
       
        $this->ViewData['familyHeadHasMembers']   = $familyHeadHasMembers;

        // view file with data
        return view($this->ModuleView.'member.show', $this->ViewData);
    }

    public function edit($encID)
    {
       
    }

    public function update(Requests $request, $encID)
    {
       
    }

    public function _storeOrUpdate($collection, $request, $default=1)
    {

        $collection->first_name         = $request->first_name;
        $collection->last_name          = $request->last_name;
        $collection->birth_date         = date('Y-m-d', strtotime($request->birth_date));

        if($default == 1){

            $collection->mobile_number      = $request->mobile_number;
            $collection->address            = $request->address;
            $collection->pincode            = $request->pincode;
            $collection->state_id           = $request->state_id;
            $collection->city_id            = $request->city_id;
            $collection->hobbies            = ( !empty($request->hobbies) && count($request->hobbies) > 0 ) ? implode(",", $request->hobbies):NULL;
        }elseif($default == 2){
             $collection->education            = $request->education;
        }

        $collection->martial_status     = !empty($request->martial_status) ? $request->martial_status : 0;
        $collection->wedding_date       = !empty($request->wedding_date) ? date('Y-m-d', strtotime($request->wedding_date)) : NULL;
        $collection->member_type        = $request->member_type;

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/storage/family-member/');
            $image->move($destinationPath, $name);
            
            $collection->photo = $name;
        }
        
        //Save data
        $collection->save();
        
        return $collection;
        
    }

    public function _calculateAge($birth_date)
    {
        $birth_date    = date('Y-m-d', strtotime($birth_date));
        $current_date  = date('Y-m-d');
        $diff=date_diff(date_create($birth_date),date_create($current_date));
        $age = $diff->format("%y year");
        return $age;
    }

    public function getStateCities(Request $request)
    {
        $state_id = $request->state_id;
        $html = '<option value="">Select City</option>';

        if(!empty($state_id)){
            $collection = $this->CitiesModel
                        ->where('state_id',$state_id)
                        ->get();
            if (!empty($collection) && sizeof($collection) > 0) 
            {
                foreach ($collection as $key => $city) 
                {
                    $html .= '<option value="'.$city['id'].'">'.$city['name'].'</option>';
                }
            }
        }
        
        $this->JsonData['cities'] = $html;

        return response()->json($this->JsonData);
    }

}
