<?php

namespace App\Enums;

/**
 * Toegestane icon-namen voor tags.
 *
 * Single source of truth voor de backend. De waarden komen 1-op-1 overeen met de
 * SVG-namen in de impakt-rn app: src/components/Icons.jsx (de `IIcon` switch/case).
 * BELANGRIJK: houd deze lijst in sync met dat bestand — de backend kan de RN-lijst
 * niet runtime ophalen. Voeg je hier een naam toe, voeg dan ook de SVG in de app toe.
 */
enum TagIcon: string
{
    case Apple = 'apple';
    case Arrow = 'arrow';
    case ArrowL = 'arrowL';
    case Bookmark = 'bookmark';
    case Calendar = 'calendar';
    case Check = 'check';
    case Chev = 'chev';
    case ChevDown = 'chevDown';
    case Close = 'close';
    case Edit = 'edit';
    case Extern = 'extern';
    case Eye = 'eye';
    case EyeOff = 'eyeOff';
    case Facebook = 'facebook';
    case Frown = 'frown';
    case Google = 'google';
    case Home = 'home';
    case Image = 'image';
    case Info = 'info';
    case Lock = 'lock';
    case Logout = 'logout';
    case Mail = 'mail';
    case Meh = 'meh';
    case Menu = 'menu';
    case Phone = 'phone';
    case Play = 'play';
    case Plus = 'plus';
    case Search = 'search';
    case Share = 'share';
    case Smile = 'smile';
    case Sparkle = 'sparkle';
    case Tag = 'tag';
    case Trash = 'trash';
    case Trend = 'trend';
    case User = 'user';
    case Video = 'video';

    /**
     * Opties voor een Filament Select: [value => label].
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $icon) => [$icon->value => $icon->value])
            ->all();
    }
}
