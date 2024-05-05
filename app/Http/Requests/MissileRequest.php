<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request de missile.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class MissileRequest extends FormRequest
{
    /**
     * Determine si l'utilisateur est autoriser à faire la requête.
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
            'resultat' => 'nullable|integer|min:0|max:6',
        ];
    }
}
