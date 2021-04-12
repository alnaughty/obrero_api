<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeneralService;
use App\Services\ImageUploader;
use Illuminate\Support\Facades\Auth;
use App\Chat;
use App\FcmToken;
use App\ChatMessage;
use App\ChatUnreadMessages;
use App\User;
class MessageController extends Controller
{
    public function __construct()
    {
    	$this->chat = new Chat;
    	$this->message = new ChatMessage;
    	$this->unread = new ChatUnreadMessages;
    	$this->gen_serve = new GeneralService;
        $this->uploader = new ImageUploader;
        $this->token = new FcmToken;
    } 

    public function index()
    {
        $user = Auth::user();
        $result = $this->chat::where('user_id', $user->id)->orWhere('user_id_x', $user->id)->with('messages')->get();
        for ($x=0; $x < count($result); $x++) { 
            $result[$x]['unread_messages'] = $this->getUnreadMessages($result[$x]['id'], $user->id);
        }
        return $result;
    }
    public function getUnreadMessages($chat_id, $user_id)
    {
        return $this->unread::where('user_id', $user_id)->where('chat_id', $chat_id)->first()->unread_messages;
    }

    public function create($user_id, $user_id_x)
    {   
        $created = $this->chat->create([
            'user_id_x' => $user_id_x,
            'user_id' => $user_id
        ]);
        if($created != null){
            $this->unread::create(['chat_id' => $created->id, 'user_id' => $user_id]);
            $this->unread::create(['chat_id' => $created->id, 'user_id' => $user_id_x]);
            return $created;
        }
    }
    public function new_message($sender_id,$chat_id,$message,$file,$tokens,$reciever_id) 
    {
        $data = [
            "sender_id" => $sender_id,
            "chat_id" => $chat_id,
            "message" => $message,
        ];
        // $data['sender_id'] = $sender_id;
        // $data['chat_id'] = $chat_id;
        // $data['message'] = $message;
        if($file != null){
            $file = $this->uploader->getStorageUrl($file,$chat_id,'messaging');
            $data['file'] = $file;
        }
        $created = $this->message::create($data);
        $created['receiver_notify'] = User::find($reciever_id)->enable_notification;
        if($created != null)
        {
            return $created;
        }
        return null;
    }
    public function getTokens($to)
    {
        return $this->token::where('user_id', $to)->get()->pluck('token');
    }
    public function send(Request $request)
    {
        $from = Auth::user()->id;
        $to = $request->to;
        $tokens = $this->getTokens($to);
        $checksender = $this->chat::where('user_id', $from)->where('user_id_x', $to)->orWhere('user_id_x', $from)->where('user_id', $to)->first();
        // $checksender = $checksender->where('user_id', $to)->orWhere('user_id_x', $to)->first();
        // return $checksender;
        if($checksender != null)
        {
            //create new message
            $message = $this->new_message($from, $checksender->id, $request->message, $request->file??null,$tokens,$to);
            if($message != null)
            {
                $this->addUnread($checksender->id, $to);
                return response()->json($message, 200);
            }
        }else{
            //create chatroom && create message
            $created = $this->create($from, $to);
            if($created != null)
            {
                $message = $this->new_message($from,$created->id,$request->message, $request->file??null,$tokens,$to);
                if($message!= null){
                    $this->addUnread($created->id, $to);
                    return response()->json($message, 200);
                }
            }
        }
        return response()->json(['message' => "Unable to send message"],422);
    }
    public function addUnread($chat_id, $user_id)
    {
        $unread = $this->unread::where('chat_id', $chat_id)->where('user_id', $user_id)->first();
        if($unread != null){
            $unread->unread_messages += 1;
            $unread->save();
        }
    }

    public function seeMessage($chat_id)
    {
        $unread = $this->unread::where('chat_id', $chat_id)->where('user_id', Auth::id())->first();
        $unread->unread_messages = 0;
        $unread->save();
    }

}
