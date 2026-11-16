<?php

namespace App\Http\Requests;

/**
 * Editing a returned Branding Support request uses the same rules as creating one;
 * files already uploaded on the request count for Submit.
 */
class BrandingUpdateRequest extends BrandingStoreRequest
{
}
