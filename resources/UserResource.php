<?php namespace Vdomah\JWTAuth\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;

class UserResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'user';

    /**
     * Create new resource collection.
     *
     * @param mixed $resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        return parent::collection($resource)->collection;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request = null)
    {
        $resourceData = $this->resource;

        Event::fire('vdomah.jwtauth.extendUserResource', [&$resourceData, $this]);

        return $resourceData;
    }
}
