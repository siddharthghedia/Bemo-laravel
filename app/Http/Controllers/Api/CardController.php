<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function list(Request $request)
    {
        $boards = Board::with(['cards' => function($query) use($request){
            $query->when($request->get('date'), function ($q) use($request){
                return $q->whereDate('created_at', $request->get('date'));
            });
            $query->when($request->get('status') === 0, function ($q) use($request){
                return $q->onlyTrashed()->get();
            });
            $query->when($request->get('status') == 1, function ($q) use($request){
                return $q->get();
            });
            $query->when($request->get('status') == 'null', function ($q) use($request){
                return $q->withTrashed()->get();
            });
        }])->where('user_id', auth()->user()->id)->get();

        return response()->json($boards, 200);
    }

    public function create(Request  $request)
    {
        $validator = \Validator::make($request->all(), [
            'board_id' => 'required',
            'title' => 'required'
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors], 400);
        }

        $board = Board::where(['user_id' => auth()->user()->id, 'id' => $request->board_id])->first();

        if(!$board)
        {
            return response()->json(['message' => 'Something went wrong'], 500);
        }

        $card = new Card();
        $card->board_id = $request->get('board_id');
        $card->title = $request->get('title');
        $card->description = $request->get('description');
        $card->order = $request->get('order');
        $card->save();

        return response()->json($card, 200);
    }

    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'card_id' => 'required',
            'board_id' => 'required',
            'title' => 'required'
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors], 400);
        }

        $board = Board::where(['user_id' => auth()->user()->id, 'id' => $request->board_id])->first();

        if(!$board)
        {
            return response()->json(['message' => 'Something went wrong'], 200);
        }

        $card = Card::find($request->get('card_id'));
        $card->board_id = $request->get('board_id');
        $card->title = $request->get('title');
        $card->description = $request->get('description');
        $card->order = $request->get('order');
        $card->save();

        return response()->json($card, 200);
    }

    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'card_id' => 'required',
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors], 400);
        }

        if(in_array($request->get('card_id'), auth()->user()->cards->pluck('id')->toArray()))
        {
            $card = Card::find($request->get('card_id'));
            if($card)
            {
                $card->delete();
                return response()->json(['message' => 'card deleted successfully'], 200);
            }
        }

        return response()->json(['message' => 'something went wrong'], 500);
    }

    public function order(Request  $request)
    {
        foreach($request->get('cards') as $item)
        {
            $card = Card::find($item['id']);
            $card->order = $item['order'];
            $card->save();
        }

        return response()->json(['message' => 'card order updated'], 200);
    }
}
