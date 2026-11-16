<?php

namespace App\Http\Requests;

/**
 * Editing a returned Meet Event request uses the same rules as creating one;
 * files already uploaded on the request count for Submit.
 */
class MeetEventUpdateRequest extends MeetEventStoreRequest
{
}
