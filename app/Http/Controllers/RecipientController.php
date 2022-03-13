<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = Recipient::paginate(15);
        $count = Recipient::count();
        if ($count == 0) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }
        $response = [
            'message' => 'list recipients',
            'result' => $datas
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Recipient::find($id);
        if ($data == null) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }
        $response = [
            'message' => sprintf('recipient name: %s', $data->name),
            'result' => $data
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipient $recipient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipient $recipient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recipient  $recipient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipient $recipient)
    {
        //
    }

    public function useParams($name)
    {
        $data = Recipient::where('name', 'like', '%' . $name . '%')
            ->get();
        $count_data = $data->count();
        if ($count_data == 0) {
            return response()->json(['message' => sprintf('recipients like %s not found', $name)], Response::HTTP_NOT_FOUND);
        }

        if ($count_data > 15) {
            $data = Recipient::where('name', 'like', '%' . $name . '%')->paginate(15);
        }

        $result = [
            'message' => sprintf('recipients like %s', $name),
            'count_result' => $count_data,
            'result' => $data
        ];
        return response()->json($result, Response::HTTP_OK);
    }
}
