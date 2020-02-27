<?php
namespace App\Http\Controllers\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Models\Generalsetting;
use App\Models\User;
use App\Classes\GeniusMailer;
use App\Models\Notification;
use App\Product;
use App\Category;
use App\Page;
use App\Slider;
use App\Subcategory;



class ApiController extends Controller
{
    public function __construct(){}
    /*Login Code*/
    public function login(Request $request)
    {
      //--- Validation Section
      $rules = [
        'email'   => 'required|email',
        'password' => 'required'
      ];

      $validator = Validator::make(Input::all(), $rules);
      
      if ($validator->fails()) {
        return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
      }

      if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        // if successful, then redirect to their intended location

        // Check If Email is verified or not
          if(Auth::guard('web')->user()->email_verified == 'No')
          {
            Auth::guard('web')->logout();
            return response()->json(array('errors' => [ 0 => 'Your Email is not Verified!' ]));   
          }

          if(Auth::guard('web')->user()->ban == 1)
          {
            Auth::guard('web')->logout();
            return response()->json(array('errors' => [ 0 => 'Your Account Has Been Banned.' ]));   
          }

          // Login Via Modal
          if(!empty($request->modal))
          {
             // Login as Vendor
            if(!empty($request->vendor))
            {
              if(Auth::guard('web')->user()->is_vendor == 2)
              {
                return response()->json(array('success' => 'true', 'status'=> 200 ,'msg'=> 'Successfully login.Go to dashboard page' ));
              }
              else {
                return response()->json(array('success' => 'true', 'status'=> 200 ,'msg'=> 'Successfully login.Go to dashboard page' ));
                }
            }
          // Login as User
          return response()->json(1);          
          }
          // Login as User
          return response()->json(array('success' => 'true', 'status'=> 200, 'msg'=> 'Successfully login.Go to dashboard page' ));
      }
      // if unsuccessful, then redirect back to the login with the form data
      return response()->json(array('errors' => [ 0 => 'Credentials Doesn\'t Match !' ]));  
    }


    /*Register*/
    public function register(Request $request)
    {
      $gs = Generalsetting::findOrFail(1);

      /*if($gs->is_capcha == 1)
      {
          $value = session('captcha_string');
          if ($request->codes != $value){
              return response()->json(array('errors' => [ 0 => 'Please enter Correct Capcha Code.' ]));    
          }       
      }*/


        $rules = [
            'name'=>'required',
            'email'   => 'required|email|unique:users',
            'password' => 'required',
            'mobile'=>'required',
            'user_type'  =>'required'
                ];
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

          $user = new User;
          $input = $request->all();        
          $input['password'] = bcrypt($request['password']);
          $token = md5(time().$request->name.$request->email);
          $input['verification_link'] = $token;
          $input['affilate_code'] = md5($request->name.$request->email);
          $input['is_vendor'] = $request->user_type;
          if(!empty($request->vendor))
          {
            //--- Validation Section
            $rules = [
              'shop_name' => 'unique:users',
              'shop_number'  => 'max:10'
                ];
            $customs = [
              'shop_name.unique' => 'This Shop Name has already been taken.',
              'shop_number.max'  => 'Shop Number Must Be Less Then 10 Digit.'
            ];

            $validator = Validator::make(Input::all(), $rules, $customs);
            if ($validator->fails()) {
              
               return response()->json(array('success' => 'false', 'status'=> 440 ,'msg'=>  $validator->getMessageBag()->toArray()));
            //return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            $input['is_vendor'] = 1;
          }
        
          $user->fill($input)->save();
          if($gs->is_verification_email == 1)
          {
          $to = $request->email;
          $subject = 'Verify your email address.';
          $msg = "Dear Customer,<br> We noticed that you need to verify your email address. <a href=".url('user/register/verify/'.$token).">Simply click here to verify. </a>";
          //Sending Email To Customer
          if($gs->is_smtp == 1)
          {
          $data = [
              'to' => $to,
              'subject' => $subject,
              'body' => $msg,
          ];

          $mailer = new GeniusMailer();
          $mailer->sendCustomMail($data);
          }
          else
          {
          $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
          mail($to,$subject,$msg,$headers);
          }
            return response()->json('We need to verify your email address. We have sent an email to '.$to.' to verify your email address. Please click link in that email to continue.');
          }
          else {

            $user->email_verified = 'Yes';
            $user->update();
          $notification = new Notification;
          $notification->user_id = $user->id;
          $notification->save();
            Auth::guard('web')->login($user); 
            return response()->json(array('success' => 'true', 'status'=> 200 ,'msg'=> 'Registration Successfully' ));
          }
    }

    public function token($token)
    {
        $gs = Generalsetting::findOrFail(1);

        if($gs->is_verification_email == 1)
          {     
        $user = User::where('verification_link','=',$token)->first();
        if(isset($user))
        {
            $user->email_verified = 'Yes';
            $user->update();
          $notification = new Notification;
          $notification->user_id = $user->id;
          $notification->save();
            Auth::guard('web')->login($user); 
            return redirect()->route('user-dashboard')->with('success','Email Verified Successfully');
        }
        }
        else {
        return redirect()->back();  
        }
    }

    /*Forget pass*/

    public function forget(Request $request)
    {
      $gs = Generalsetting::findOrFail(1);
      $input =  $request->all();


      if (User::where('email', '=', $request->email)->count() > 0) {
      // user found
      $admin = User::where('email', '=', $request->email)->firstOrFail();
      $autopass = str_random(8);
      $input['password'] = bcrypt($autopass);
      $admin->update($input);
      //dd($input);
      $subject = "Reset Password Request";
      $msg = "Your New Password is : ".$autopass;
      if($gs->is_smtp == 1)
      {
          $data = [
                  'to' => $request->email,
                  'subject' => $subject,
                  'body' => $msg,
          ];

          $mailer = new GeniusMailer();
          $mailer->sendCustomMail($data);                
      }
      else
      {
          $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
          mail($request->email,$subject,$msg,$headers);            
      }
      return response()->json('Your Password Reseted Successfully. Please Check your email for new Password.');
      }
      else{
      // user not found
      return response()->json(array('errors' => [ 0 => 'No Account Found With This Email.' ]));    
      }  
    }
    /*Show all Product details*/
    public function AllProdDtl(){
      $productList = Product::all();
      
         return response()->json(array('success' => 'true', 'status'=> 200 ,'data'=>$productList));
      //return response()->json($productList);
    }

    // Show Product details by Category
    public function ProdDtlByCat(Request $request){
      $productList = Product::where('category_id','=',$request->category_id)->get();
      return response()->json(array('success' => 'true', 'status'=> 200 ,'data'=>$productList));
    }

    // Show All Category Details
    public function AllCatDtl(){
      $categoryList = Category::all();
      return response()->json(array('success' => 'true', 'status'=> 200 ,'data'=>$categoryList));
    }

    // Get Page Data
    public function PageData(Request $request){
      $pageData = Page::where('slug','=',$request->pageName)->get();
      return response()->json(array('success' => 'true', 'status'=> 200 ,'data'=>$pageData));
    }

    //Get Slider Data
    public function SliderData(){
      $sliderData = Slider::all();
      return response()->json(array('success' => 'true', 'status'=> 200 ,'data'=>$sliderData));
    }

    //Get SubCat by Category
    public function ListSubCatByCat(Request $request){
      $subCatData = Subcategory::where('category_id','=',$request->category_id)->get();
      return response()->json(array('success' => 'true', 'status'=> 200 ,'data'=>$subCatData));
    }

    
}
