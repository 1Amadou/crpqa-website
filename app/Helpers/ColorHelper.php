<?php
namespace App\Helpers;
class ColorHelper {
    public static function isLightColor(string $hexColor): bool {
        // Logique pour déterminer si la couleur est claire
        if (empty($hexColor)) {
            return false; // Assure un retour booléen même en cas d'erreur
        }
    
        // Exemple de logique (à adapter selon ton besoin)
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
    
        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
    
        return $brightness > 128; // Retourne true si la couleur est claire, sinon false
    }
    
}