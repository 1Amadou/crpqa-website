<?php

namespace App\Traits;

use Illuminate\Support\Facades\App; // Nécessaire pour App::getLocale()

trait HasLocalizedFields
{
    /**
     * Récupère la traduction d'un champ pour la locale donnée ou la locale actuelle.
     * Le modèle doit avoir une propriété protected $localizedFields = ['base_field_name1', 'base_field_name2'];
     * Et les colonnes en base de données doivent être nommées base_field_name_fr, base_field_name_en, etc.
     */
    public function getTranslation(string $field, ?string $locale = null, bool $useFallback = true)
    {
        $locale = $locale ?: App::getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en'); // Assurez-vous que 'fallback_locale' est 'en' ou 'fr' dans config/app.php

        // Vérifie si le champ de base est déclaré comme localisable dans le modèle
        if (!property_exists($this, 'localizedFields') || !in_array($field, $this->localizedFields)) {
            // Si le champ n'est pas censé être localisé, retourne la valeur brute ou null.
            // Cela peut arriver si on appelle getTranslation sur un champ non listé dans $localizedFields.
            return $this->attributes[$field] ?? null;
        }

        $localizedFieldWithLocale = $field . '_' . $locale;

        // Vérifie si l'attribut avec la locale spécifique existe et n'est pas vide
        if (array_key_exists($localizedFieldWithLocale, $this->attributes) && !empty($this->attributes[$localizedFieldWithLocale])) {
            return $this->attributes[$localizedFieldWithLocale];
        }

        // Si le fallback est activé et que la locale actuelle n'est pas la locale de fallback
        if ($useFallback && $locale !== $fallbackLocale) {
            $fallbackFieldWithLocale = $field . '_' . $fallbackLocale;
            if (array_key_exists($fallbackFieldWithLocale, $this->attributes) && !empty($this->attributes[$fallbackFieldWithLocale])) {
                return $this->attributes[$fallbackFieldWithLocale];
            }
        }
        
        // Si aucune traduction (ni primaire, ni fallback) n'est trouvée et n'est pas vide,
        // on pourrait retourner une chaîne vide, le nom du champ, ou null.
        // Retourner null est souvent le plus sûr pour éviter d'afficher des choses non désirées.
        return null; 
    }

    /**
     * Surcharge de l'accesseur magique pour permettre un accès direct comme $model->title.
     * Cela retournera la traduction pour la locale actuelle ou le fallback.
     */
    public function getAttribute($key)
    {
        // Si la clé demandée est un champ localisé déclaré dans $localizedFields.
        if (property_exists($this, 'localizedFields') && in_array($key, $this->localizedFields)) {
            return $this->getTranslation($key); // Utilise notre méthode getTranslation
        }
        // Sinon, comportement par défaut de Eloquent.
        return parent::getAttribute($key);
    }

    // Optionnel: Si vous voulez aussi gérer la sauvegarde via $model->title = 'valeur';
    // il faudrait surcharger setAttribute. Pour l'instant, on se concentre sur la lecture.
    /*
    public function setAttribute($key, $value)
    {
        if (property_exists($this, 'localizedFields') && in_array($key, $this->localizedFields)) {
            $locale = App::getLocale();
            $localizedFieldWithLocale = $key . '_' . $locale;
            $this->attributes[$localizedFieldWithLocale] = $value;
            return $this;
        }
        return parent::setAttribute($key, $value);
    }
    */
}