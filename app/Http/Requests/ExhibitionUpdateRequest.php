<?php

namespace App\Http\Requests;

/**
 * Editing a returned Exhibition / Sponsorship request uses the same rules as creating one;
 * files already uploaded on the request count for Submit.
 */
class ExhibitionUpdateRequest extends ExhibitionStoreRequest
{
}
