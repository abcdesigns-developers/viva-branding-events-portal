<?php

namespace App\Http\Requests;

/**
 * Editing a returned New Experience Centre request uses the same rules as creating one;
 * files already uploaded on the request count for Submit.
 */
class ExperienceCentreUpdateRequest extends ExperienceCentreStoreRequest
{
}
