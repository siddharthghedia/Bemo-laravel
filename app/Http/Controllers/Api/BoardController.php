<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function create(Request  $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors], 400);
        }

        $board = new Board();
        $board->user_id = auth()->user()->id;
        $board->name = $request->get('name');
        $board->save();

        return response()->json($board,200);
    }

    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'board_id' => 'required'
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors], 400);
        }

        $board = Board::where(['user_id'=> auth()->user()->id, 'id' => $request->get('board_id')])->first();
        if($board)
        {
            $board->cards->delete();
            $board->delete();

            return response()->json(['message' => 'board deleted successfully'], 200);
        }

        return response()->json(['message' => 'Board you are trying to delete is not found'], 500);
    }
}
