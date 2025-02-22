<?php

namespace App\Services\Integrations\Aifabu\Enums;

enum ChainType: int
{
    case SHORT_LINK = 1;
    case WEIXIN_LINK = 2;
    case WEIXIN_LINK_ENTERPRISE_WECHAT = 3;
    case WEIXIN_LINK_CARD_SYSTEM = 4;
    case ENTERPRISE_WECHAT_LINK_CARD_SYSTEM = 5;

    public function label(): string
    {
        return match ($this) {
            self::SHORT_LINK => '短链(短链/外链)',
            self::WEIXIN_LINK => '微信外链(短链/外链)',
            self::WEIXIN_LINK_ENTERPRISE_WECHAT => '外链-企微获客链接(/短链外链)',
            self::WEIXIN_LINK_CARD_SYSTEM => '微信外链(卡片系统)',
            self::ENTERPRISE_WECHAT_LINK_CARD_SYSTEM => '外链-企微获客链接(卡片系统)',
        };
    }

    public static function getLabelValuesArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}