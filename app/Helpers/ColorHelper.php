<?php


namespace App\Helpers;

class ColorHelper
{
    /**
     * Convertit un code hexadécimal en rgba.
     *
     * @param string $hexColor Le code hexadécimal de la couleur.
     * @param float $alpha Le niveau de transparence (0 à 1).
     * @return string Code rgba correspondant.
     */
    public static function hexToRgba(string $hexColor, float $alpha = 1): string
    {
        // Supprimer le # s'il est présent
        $hexColor = ltrim($hexColor, '#');

        // Vérifier si la couleur est valide (3 ou 6 caractères hexadécimaux)
        if (!preg_match('/^[0-9A-Fa-f]{3,6}$/', $hexColor)) {
            return "rgba(0,0,0,{$alpha})"; // Valeur par défaut en cas d'erreur
        }

        // Si la couleur est sous format réduit (ex: "FFF"), l'étendre
        if (strlen($hexColor) === 3) {
            $hexColor = preg_replace('/(.)/', '$1$1', $hexColor);
        }

        // Conversion en RGB
        list($r, $g, $b) = sscanf($hexColor, "%02x%02x%02x");

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}
