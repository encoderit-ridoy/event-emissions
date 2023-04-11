<?php

namespace App\Http\Controllers;

use App\Models\ContentManagement;
use Illuminate\Http\Request;

class ContentManageController extends Controller
{
    public function index(Request $request)
    {
        $contents = ContentManagement::latest()->paginate($request->per_page ?? 25);

        return response()->json([
            'status' => 'Success',
            'contents'   => $contents
        ], 200);
    }

    public function store(Request $request)
    {
        $validate_data = [
            'title'   => 'sometimes|required|string|unique:content_management,title',
            'content' => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $content = ContentManagement::create($validator);

        return $this->getSingleData($content->id, 'Content Created Successfully.', 201);
    }

    public function update(Request $request)
    {
        $validate_data = [
            'id'      => 'required|integer|exists:content_management,id',
            'title'   => 'sometimes|required|string|unique:content_management,title,' . $request->id,
            'content' => 'required|string',
        ];
        $validator = $request->validate($validate_data);
        $content = ContentManagement::findOrFail($request->id);
        $content->update($validator);

        return $this->getSingleData($content->id, 'Content Updated Successfully.', 200);
    }

    public function getSingleData($id, $message = 'Content Found.', $code = 200)
    {
        $content = ContentManagement::findOrFail($id);

        return response()->json([
            'message' => $message,
            'content'   => $content
        ], $code);
    }

    public function destroy($id)
    {
        $content = ContentManagement::findOrFail($id);
        $content->delete();

        return response()->json([
            'message' => 'Content Deleted Successfully.',
        ], 200);
    }
}
