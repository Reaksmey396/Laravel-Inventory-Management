<?php
    function apiResponse($data = null, $status = 200, $message = ""){
        return response()->json([
            'data' => $data,
            'status' => $status,
            'message' => $message,
            'smg' => $message,
        ], $status ?: 200);
    }
