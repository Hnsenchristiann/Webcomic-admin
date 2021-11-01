<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->getAllPermissions()->toArray()
            ),
            'avatar' => $this->profile_photo_url,
            // 'token_transactions' => $this->tokenTransactions,
            'total_tokens' => $this->total_tokens,
            'subscriptions' => json_decode($this->subscriptions),
            'favorites' => json_decode($this->favorites),
            'bookmark' => json_decode($this->bookmark),
            'purchase_history' => json_decode($this->purchase_history)
        ];
    }
}
