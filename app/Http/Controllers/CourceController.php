<?php
namespace App\Http\Controllers;
use App\Models\Cource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userID=Auth::user()->id;
        //$cources = Cource::table('cource')->where('creator_id', $userID)->get();
        //$cources = Cource::latest()->where('creator_id', $userID)->get();

        $cources = Cource::latest()->where('creator_id', $userID)->paginate(5);
        return view('cources.index',compact('cources'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cources.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userID=Auth::user()->id;
        $storeData = $request->validate([
            'cource_name' => 'required|max:255',
            'cource_detail' => 'required|max:500',
            'enrollment_key' => 'required|max:20|unique:cources',


        ]);
        $cource = Cource::create([
            'cource_name'=>$storeData['cource_name'],
            'cource_detail' => $storeData['cource_detail'],
            'creator_id' => $userID,
            'enrollment_key' => $storeData['enrollment_key'],

        ]);
        return redirect()->route('cources.index')->with('success','cource created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cource  $cource
     * @return \Illuminate\Http\Response
     */
    public function show(Cource $cource)
    {
        return view('cources.show',compact('cource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cource  $cource
     * @return \Illuminate\Http\Response
     */
    public function edit(Cource $cource)
    {
        $userID=Auth::user()->id;
        $CreatorID = $cource->creator_id;

        if ($userID==$CreatorID){
        return view('cources.edit',compact('cource'));
        }
        else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cource  $cource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cource $cource)
    {
        $userID=Auth::user()->id;
        $CreatorID = $cource->creator_id;

        if ($userID==$CreatorID){
            $request->validate([
                'cource_name' => 'required',
                'cource_detail' => 'required',
            ]);

            $cource->update($request->all());
            return redirect()->route('cources.index')
                            ->with('success','cource updated successfully');
        }
        else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cource  $cource
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cource $cource)
    {
        $cource->delete();
        return redirect()->route('cources.index')
                        ->with('success','cource deleted successfully');
    }

}
