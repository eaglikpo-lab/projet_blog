<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->titre,
            "description" => $this->contenu,
            "image" => $this->image,
            'mots_cles' => $this->mots_cles,
            'categorie_id' => $this->categorie_id,
            "created_at" => $this->created_at,
            "created_at_formatted" =>Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
            "updated_at"=> $this->updated_at,
            "updated_at_formatted" =>Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
        ];
    }
}
