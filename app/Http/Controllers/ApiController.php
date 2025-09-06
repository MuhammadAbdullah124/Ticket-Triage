<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ClassifyTicket;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
  public function index(Request $request)
  {
    $tickets = Ticket::orderBy('created_at', 'desc')->get();

    return response()->json([
      'status' => 'success',
      'response' => $tickets,
    ]);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'subject' => 'required|string|max:255',
      'body' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'errors' => $validator->errors(),
      ], 422);
    }

    $ticket = Ticket::create([
      'subject' => $request->subject,
      'body' => $request->body,
      'status' => 'open',
    ]);

    return response()->json([
      'status' => 'success',
      'response' => $ticket,
    ], 201);
  }

  public function show(string $id)
  {
    $ticket = Ticket::findOrFail($id);

    return response()->json([
      'status' => 'success',
      'response' => $ticket,
    ]);
  }

  public function update(Request $request, string $id)
  {
    $ticket = Ticket::findOrFail($id);

    $ticket->update($request->only(['status', 'category', 'note']));

    return response()->json([
      'status' => 'success',
      'response' => $ticket,
    ]);
  }

  public function classify(string $id)
  {
    $ticket = Ticket::findOrFail($id);
    ClassifyTicket::dispatchSync($ticket);

    return response()->json([
      'status' => 'success',
      'message' => 'Ticket classified successfully',
      'ticket' => $ticket->fresh()
    ]);
  }



  public function stats()
  {
    $total = Ticket::count();
    $byStatus = Ticket::select('status', DB::raw('count(*) as count'))
      ->groupBy('status')->pluck('count', 'status');
    $byCategory = Ticket::select('category', DB::raw('count(*) as count'))
      ->groupBy('category')->pluck('count', 'category');
    $avgConfidence = Ticket::avg('confidence');

    return response()->json([
      'status' => 'success',
      'response' => [
        'total' => $total,
        'byStatus' => $byStatus,
        'byCategory' => $byCategory,
        'avgConfidence' => $avgConfidence
      ]
    ]);
  }
}
