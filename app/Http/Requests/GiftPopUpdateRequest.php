<?php

namespace App\Http\Requests;

/**
 * Editing a returned Gift & POP request uses the same rules as creating one;
 * files already uploaded on the request count for Submit.
 */
class GiftPopUpdateRequest extends GiftPopStoreRequest
{
}
