<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Blog API",
 *     version="1.0.0",
 *     description="Documentation de l’API du projet Blog"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Serveur principal"
 * )
 */
class OpenApi
{
    // Ce fichier ne contient que des annotations.
}



/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article Resource",
 *     description="Structure d'un article retourné par l'API",
 * 
 *     @OA\Property(property="id", type="integer", example=1, description="Identifiant unique de l'article"),
 *     @OA\Property(property="title", type="string", example="Introduction à Laravel", description="Titre de l'article"),
 *     @OA\Property(property="description", type="string", example="Laravel est un framework PHP moderne...", description="Contenu de l'article"),
 *     @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/images/article1.jpg", description="URL complète de l'image si disponible"),
 *     @OA\Property(property="keywords", type="string", nullable=true, example="laravel, php, framework", description="Mots-clés liés à l'article"),
 *     @OA\Property(property="categorie", type="string", example="Programmation", description="Nom de la catégorie associée"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-23T09:30:00Z", description="Date de création brute"),
 *     @OA\Property(property="created_at_formatted", type="string", example="2025-10-23 09:30:00", description="Date de création formatée"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-23T10:00:00Z", description="Date de mise à jour brute"),
 *     @OA\Property(property="updated_at_formatted", type="string", example="2025-10-23 10:00:00", description="Date de mise à jour formatée")
 * )
 * 
 * * // Si tu renvoies une pagination Laravel, définis un wrapper :
 * @OA\Schema(
 *   schema="PaginatedArticles",
 *   type="object",
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Article")
 *   ),
 *   @OA\Property(
 *     property="links",
 *     type="object",
 *     @OA\Property(property="first", type="string", nullable=true),
 *     @OA\Property(property="last", type="string", nullable=true),
 *     @OA\Property(property="prev", type="string", nullable=true),
 *     @OA\Property(property="next", type="string", nullable=true)
 *   ),
 *   @OA\Property(
 *     property="meta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from", type="integer", nullable=true),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="to", type="integer", nullable=true),
 *     @OA\Property(property="total", type="integer", example=150)
 *   )
 * )
 */
class Schemas
{
    // Ce fichier ne contient que des annotations.
}