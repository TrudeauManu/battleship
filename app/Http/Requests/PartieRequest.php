<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de partie.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class PartieRequest extends FormRequest
{
    /**
     * Determine si l'utilisateur est autorisé à faire la requête.
    */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retourne les règles de validation qui s'applique à la requête.
 *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'adversaire' => 'required|string',
        ];
    }
}
