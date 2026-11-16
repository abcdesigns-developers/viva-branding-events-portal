<?php

namespace App\Http\Requests;

/**
 * Editing a returned Presentation request uses the same rules as creating one;
 * files already uploaded on the request count for Submit.
 */
class PresentationUpdateRequest extends PresentationStoreRequest
{
}
