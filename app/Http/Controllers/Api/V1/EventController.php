<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Event, EventImage };
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use mysql_xdevapi\Exception;

class EventController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['publicUpcoming']]);
    }

    /**
     * @OA\Post(
     ** path="/api/v1/event",
     *   tags={"CMS - Event"},
     *   summary="Create Event",
     *   operationId="event",
     *
     *   @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="date",
     *      in="query",
     *      required=true,
     *      example="2022-06-03",
     *      @OA\Schema(
     *           type="string",
     *           format="date",
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="time",
     *      in="query",
     *      required=true,
     *      example="18:00",
     *      @OA\Schema(
     *           type="string",
     *           format="time",
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="location",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\RequestBody(
     *      required=true,
     *        @OA\MediaType(
     *          mediaType="multipart/form-data",
     *           @OA\Schema(
     *           @OA\Property(
     *             description="file to upload",
     *             property="image",
     *             type="file",
     *           ),
     *             required={"image"}
     *           )
     *        )
     *   ),
     *   security={
     *     {"bearer": {}}
     *   },
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    public function create(Request $req) {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|min:2|max:255',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|min: 2|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $event = Event::create($req->all());
            if (!$event) throw new \Exception("Failed to create event, please try again.");

            // store and get img url
            $imgUrl = $this->storeImage($req->file('image'), 3);
            $eventImage = EventImage::create([
               'path' => $imgUrl,
                'event_id' => $event->id
            ]);

            return response()->json([
                'message' => 'Event data created successfully.',
                'event' => $event,
                'image' => $eventImage
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     ** path="/api/v1/event/{event_id}",
     *   tags={"CMS - Event"},
     *   summary="Update Event",
     *   operationId="update",
     *
     *   @OA\Parameter(
     *      name="event_id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="date",
     *      in="query",
     *      required=true,
     *      example="2022-06-03",
     *      @OA\Schema(
     *           type="string",
     *           format="date",
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="time",
     *      in="query",
     *      required=true,
     *      example="18:00",
     *      @OA\Schema(
     *           type="string",
     *           format="time",
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="location",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *  @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="image",
     *                     type="file",
     *                ),
     *             )
     *         )
     *     ),
     *   security={
     *     {"bearer": {}}
     *   },
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    public function update(Request $req, $id) {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|min:2|max:255',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|min: 2|max:255',
            'image' => 'image|mimes:jpg,png,jpeg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $updatedEvent = Event::find($id);
            if (!$updatedEvent) throw new \Exception(
                "Failed to update event please make sure that event_id is correct."
            ); else $updatedEvent->update($req->except(['image']));

            if ($req->hasFile('image')) {
                $image = EventImage::where('event_id', $id)->first();
                $newImgUrl = $this->updateImage($req->file('image'), $image->path);
                $image->path = $newImgUrl;
                $image->save();
            }

            return response()->json([
                'message' => 'Event data updated successfully.',
                'event' => $updatedEvent,
                'eventImage' => $image
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     ** path="/api/v1/event/{event_id}",
     *   tags={"CMS - Event"},
     *   summary="Delete Event",
     *   operationId="delete",
     *
     *   @OA\Parameter(
     *      name="event_id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *   security={
     *     {"bearer": {}}
     *   },
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    public function delete($id) {
        try {
           $event = Event::find($id);
           if (!$event) throw new \Exception(
               "Delete event failed, please make sure event_id is correct."
           ); else $event->delete();

           return response()->json([
               'message' => 'Event successfully deleted.',
               'event_id' => $id
           ]);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function storeImage($file) {
        // defining file
        $fileName = Carbon::now()->format('YmdHis').'.'.$file->extension();
        $file->move(public_path('event_images'), $fileName);
        $imgUrl = asset('event_images/'.$fileName);

        return $imgUrl;
    }

    public function updateImage($file, $oldfile) {
        // unlink or delete old file from storage
        $oldPath = explode('/', $oldfile);
        $directory = $oldPath[3];
        $oldFleName = $oldPath[4];

        if (file_exists(public_path($directory.'/'.$oldFleName))) {
            unlink(public_path($directory.'/'.$oldFleName));
        }

        // defining file & store
        $fileName = Carbon::now()->format('YmdHis').'.'.$file->extension();
        $file->move(public_path('event_images'), $fileName);
        $imgUrl = asset('event_images/'.$fileName);

        return $imgUrl;
    }

    /**
     * @OA\Get(
     ** path="/api/v1/event",
     *   tags={"CMS - Event"},
     *   summary="Event List",
     *   operationId="showList",
     *   security={
     *     {"bearer": {}}
     *   },
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    public function showList() {
        $eventList = Event::latest()->paginate(8);
        return response()->json($eventList);
    }

    /**
     * @OA\Get(
     ** path="/api/v1/event/{event_id}",
     *   tags={"CMS - Event"},
     *   summary="Event Detail",
     *   operationId="showDetail",
     *   security={
     *     {"bearer": {}}
     *   },
     *   @OA\Parameter(
     *      name="event_id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    public function showDetail($id) {
        $eventDetail = Event::where('id', $id)->with('image')->first();
        return response()->json($eventDetail);
    }

    // PUBLIC ROUTES
    /**
     * @OA\Get(
     ** path="/event/upcomings",
     *   tags={"Public - Upcoming Event"},
     *   summary="Upcoming Event",
     *   operationId="publicUpcoming",
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    public function publicUpcoming() {
        $upcomings = Event::select('id', 'name', 'date', 'time', 'location')->with('image')->latest()->limit(4)->get();
        return response()->json($upcomings);
    }
}
