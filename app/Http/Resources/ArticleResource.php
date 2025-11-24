<?php

namespace App\Http\Resources;

use Carbon\Carbon;
// use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


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
            //'image' => $this->image ? asset('storage/' . $this->image) : null,   //avant

            // ✅ Génère l'URL complète HTTPS automatiquement
            //'image' => $this->image ? Storage::url($this->image) : null,  //càd: Retourne : "https://projetblog-production.up.railway.app/storage/images/xxx.jpg"
            // Ou alternative :
            'image' => $this->image ? url('storage/' . $this->image) : null,
            'keywords' => $this->mots_cles,
            'categorie' => $this->categorie->nom,
            "created_at" => $this->created_at,
            "created_at_formatted" =>Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
            "updated_at"=> $this->updated_at,
            "updated_at_formatted" =>Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
        ];
    }
}
