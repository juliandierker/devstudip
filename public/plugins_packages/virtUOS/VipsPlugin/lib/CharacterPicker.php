<?php
/*
 * CharacterPicker.php - Vips plugin for Stud.IP
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class CharacterPicker
{
    private $activeCharacterSet;



    public function __construct($active_character_set = null)
    {
        $availableCharacterSets = self::availableCharacterSets();

        if (isset($availableCharacterSets[$active_character_set])) {
            $this->activeCharacterSet = $active_character_set;
        } else {
            $this->activeCharacterSet = key($availableCharacterSets);
        }
    }



    public function render()
    {
        global $vipsTemplateFactory;

        $template = $vipsTemplateFactory->open('sheets/character_picker');
        $template->available_character_sets = self::availableCharacterSets();
        $template->active_character_set     = $this->activeCharacterSet;

        return $template->render();
    }



    public static function availableCharacterSets()
    {
        static $availableCharacterSets;

        if (!$availableCharacterSets) {
            $availableCharacterSets = self::loadAvailableCharacterSets();
        }

        return $availableCharacterSets;
    }

    private static function loadAvailableCharacterSets()
    {
        return [
            'ipa' => [  // IPA characters
                'name'        => 'ipa',
                'title'       => _vips('IPA-Zeichen'),
                'characters'  => [
                    [
                        'chars' => [
                            '0061' => _vips('ungerundeter offener Vorderzungenvokal'),
                            '0251' => _vips('ungerundeter offener Hinterzungenvokal'),
                            '00e6' => _vips('ungerundeter fast offener Vorderzungenvokal'),
                            '0250' => _vips('fast offener Zentralvokal'),
                            '0251+0303' => _vips('ungerundeter offener Hinterzungennasalvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '0062' => _vips('stimmhafter bilabialer Plosiv'),
                            '03b2' => _vips('stimmhafter bilabialer Frikativ'),
                            '0253' => _vips('stimmhafter bilabialer Implosiv'),
                            '0299' => _vips('stimmhafter bilabialer Vibrant')
                        ]
                    ],
                    [
                        'chars' => [
                            '0063' => _vips('stimmloser palataler Plosiv'),
                            '0255' => _vips('stimmloser alveopalataler Frikativ'),
                            '00e7' => _vips('stimmloser palataler Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0064' => _vips('stimmhafter alveolarer Plosiv'),
                            '00f0' => _vips('stimmhafter dentaler Frikativ'),
                            '0064+0361+0292' => _vips('stimmhafte postalveolare Affrikate'),
                            '0256' => _vips('stimmhafter retroflexer Plosiv'),
                            '0257' => _vips('stimmhafter dentaler Implosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '0065' => _vips('ungerundeter halbgeschlossener Vorderzungenvokal'),
                            '0259' => _vips('mittlerer Zentralvokal'),
                            '025a' => _vips('rhotizierter mittlerer Zentralvokal'),
                            '0275' => _vips('gerundeter halbgeschlossener Zentralvokal'),
                            '0258' => _vips('ungerundeter halbgeschlossener Zentralvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '025b' => _vips('ungerundeter halboffener Vorderzungenvokal'),
                            '025c' => _vips('ungerundeter halboffener Zentralvokal'),
                            '025d' => _vips('rhotizierter ungerundeter halboffener Zentralvokal'),
                            '025b+0303' => _vips('ungerundeter halboffener Vorderzungennasalvokal'),
                            '025e' => _vips('gerundeter halboffener Zentralvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '0066' => _vips('stimmloser labiodentaler Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0261' => _vips('stimmhafter velarer Plosiv'),
                            '0260' => _vips('stimmhafter velarer Implosiv'),
                            '0262' => _vips('stimmhafter uvularer Plosiv'),
                            '029b' => _vips('stimmhafter uvularer Implosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '0068' => _vips('stimmloser glottaler Frikativ'),
                            '0265' => _vips('stimmhafter labiopalataler Approximant'),
                            '0266' => _vips('stimmhafter glottaler Frikativ'),
                            '0127' => _vips('stimmloser pharyngaler Frikativ'),
                            '0267' => _vips('stimmloser velopalataler Frikativ'),
                            '029c' => _vips('stimmloser epiglottaler Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0069' => _vips('ungerundeter geschlossener Vorderzungenvokal'),
                            '026a' => _vips('ungerundeter zentralisierter fast geschlossener Vorderzungenvokal'),
                            '00ef' => _vips('ungerundeter zentralisierter fast geschlossener Zentralvokal'),
                            '0268' => _vips('ungerundeter geschlossener Zentralvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '006a' => _vips('stimmhafter palataler Approximant'),
                            '029d' => _vips('stimmhafter palataler Frikativ'),
                            '025f' => _vips('stimmhafter palataler Plosiv'),
                            '0284' => _vips('stimmhafter palataler Implosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '006b' => _vips('stimmloser velarer Plosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '006c' => _vips('stimmhafter lateraler alveolarer Approximant'),
                            '026b' => _vips('velarisierter lateraler alveolarer Approximant'),
                            '026c' => _vips('stimmloser lateraler alveolarer Frikativ'),
                            '029f' => _vips('stimmhafter lateraler velarer Approximant'),
                            '026d' => _vips('stimmhafter lateraler retroflexer Approximant'),
                            '026e' => _vips('stimmhafter lateraler alveolarer Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '006d' => _vips('stimmhafter bilabialer Nasal'),
                            '0271' => _vips('stimmhafter labiodentaler Nasal')
                        ]
                    ],
                    [
                        'chars' => [
                            '006e' => _vips('stimmhafter alveolarer Nasal'),
                            '014b' => _vips('stimmhafter velarer Nasal'),
                            '0272' => _vips('stimmhafter palataler Nasal'),
                            '0274' => _vips('stimmhafter uvularer Nasal'),
                            '0273' => _vips('stimmhafter retroflexer Nasal')
                        ]
                    ],
                    [
                        'chars' => [
                            '006f' => _vips('gerundeter halbgeschlossener Hinterzungenvokal'),
                            '0254' => _vips('gerundeter halboffener Hinterzungenvokal'),
                            '0153' => _vips('gerundeter halboffener Vorderzungenvokal'),
                            '00f8' => _vips('gerundeter halbgeschlossener Vorderzungenvokal'),
                            '0252' => _vips('gerundeter offener Hinterzungenvokal'),
                            '0254+0303' => _vips('gerundeter halboffener Hinterzungennasalvokal'),
                            '0276' => _vips('gerundeter offener Vorderzungenvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '0070' => _vips('stimmloser bilabialer Plosiv'),
                            '0278' => _vips('stimmloser bilabialer Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0071' => _vips('stimmloser uvularer Plosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '0072' => _vips('stimmhafter alveolarer Vibrant'),
                            '027e' => _vips('stimmhafter alveolarer Tap'),
                            '0281' => _vips('stimmhafter uvularer Frikativ'),
                            '0279' => _vips('stimmhafter alveolarer Approximant'),
                            '027b' => _vips('stimmhafter retroflexer Approximant'),
                            '0280' => _vips('stimmhafter uvularer Vibrant'),
                            '027d' => _vips('stimmhafter retroflexer Flap'),
                            '027a' => _vips('lateraler alveolarer Flap')
                        ]
                    ],
                    [
                        'chars' => [
                            '0073' => _vips('stimmloser alveolarer Frikativ'),
                            '0283' => _vips('stimmloser postalveolarer Frikativ'),
                            '0282' => _vips('stimmloser retroflexer Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0074' => _vips('stimmloser alveolarer Plosiv'),
                            '03b8' => _vips('stimmloser dentaler Frikativ'),
                            '0074+0361+0283' => _vips('stimmlose postalveolare Affrikate'),
                            '0074+0361+0073' => _vips('stimmlose alveolare Affrikate'),
                            '0288' => _vips('stimmloser retroflexer Plosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '0075' => _vips('gerundeter geschlossener Hinterzungenvokal'),
                            '028a' => _vips('gerundeter zentralisierter fast geschlossener Hinterzungenvokal'),
                            '028a+0308' => _vips('gerundeter fast geschlossener Zentralvokal'),
                            '0289' => _vips('gerundeter geschlossener Zentralvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '0076' => _vips('stimmhafter labiodentaler Frikativ'),
                            '028c' => _vips('ungerundeter halboffener Hinterzungenvokal'),
                            '028b' => _vips('stimmhafter labiodentaler Approximant'),
                            '2c71' => _vips('stimmhafter labiodentaler Flap')
                        ]
                    ],
                    [
                        'chars' => [
                            '0077' => _vips('labialisierter stimmhafter velarer Approximant'),
                            '028d' => _vips('stimmloser labiovelarer Frikativ'),
                            '026f' => _vips('ungerundeter geschlossener Hinterzungenvokal'),
                            '0270' => _vips('stimmhafter velarer Approximant')
                        ]
                    ],
                    [
                        'chars' => [
                            '0078' => _vips('stimmloser velarer Frikativ'),
                            '03c7' => _vips('stimmloser uvularer Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0079' => _vips('gerundeter geschlossener Vorderzungenvokal'),
                            '028e' => _vips('stimmhafter lateraler palataler Approximant'),
                            '0263' => _vips('stimmhafter velarer Frikativ'),
                            '028f' => _vips('gerundeter zentralisierter fast geschlossener Vorderzungenvokal'),
                            '0264' => _vips('ungerundeter halbgeschlossener Hinterzungenvokal')
                        ]
                    ],
                    [
                        'chars' => [
                            '007a' => _vips('stimmhafter alveolarer Frikativ'),
                            '0292' => _vips('stimmhafter postalveolarer Frikativ'),
                            '0290' => _vips('stimmhafter retroflexer Frikativ'),
                            '0291' => _vips('stimmhafter alveolopalataler Frikativ')
                        ]
                    ],
                    [
                        'chars' => [
                            '0294' => _vips('stimmloser glottaler Plosiv'),
                            '0295' => _vips('stimmhafter pharyngaler Frikativ'),
                            '02a2' => _vips('stimmhafter epiglottaler Frikativ'),
                            '02a1' => _vips('stimmloser epiglottaler Plosiv')
                        ]
                    ],
                    [
                        'chars' => [
                            '007c' => _vips('untergeordnete Intonationsgruppe'),
                            '2016' => _vips('übergeordnete Intonationsgruppe')
                        ]
                    ],
                    [
                        'chars' => [
                            '02c8' => _vips('primäre Betonung'),
                            '02cc' => _vips('sekundäre Betonung')
                        ]
                    ],
                    [
                        'chars' => [
                            '02d0' => _vips('Längezeichen'),
                            '02d1' => _vips('halblang'),
                            '25cc+0306' => _vips('extra kurz')
                        ]
                    ],
                    [
                        'chars' => [
                            '002e' => _vips('Silbengrenze'),
                            '25cc+0361+25cc' => _vips('Liaisonbogen'),
                            '25cc+035c+25cc' => _vips('Liaisonbogen'),
                            '203f' => _vips('Bindung'),
                            '25cc+0308' => _vips('zentralisiert'),
                            '25cc+0303' => _vips('nasaliert'),
                            '25cc+0325' => _vips('stimmlos'),
                            '25cc+030a' => _vips('stimmlos'),
                            '25cc+032c' => _vips('stimmhaft'),
                            '25cc+0329' => _vips('silbisch'),
                            '1d4a' => _vips('silbisch oder Schwa'),
                            '02b3' => _vips('optionales R'),
                            '25cc+02de' => _vips('rhotiziert'),
                            '02b0' => _vips('aspiriert'),
                            '02b7' => _vips('labialisiert'),
                            '02b2' => _vips('palatalisiert'),
                            '25cc+0334' => _vips('velarisiert oder pharyngalisiert'),
                            '25cc+031d' => _vips('angehoben'),
                            '25cc+031e' => _vips('gesenkt'),
                            '25cc+031f' => _vips('weiter vorne'),
                            '25cc+0320' => _vips('weiter hinten')
                        ]
                    ]
                ],
                'optional'    => [
                    [
                        'chars' => [
                            '25cc+030b' => _vips('extra hoch'),
                            '25cc+0301' => _vips('hoch'),
                            '25cc+0304' => _vips('mittelhoch'),
                            '25cc+0300' => _vips('niedrig'),
                            '25cc+030f' => _vips('extra niedrig'),
                            '25cc+030c' => _vips('steigend'),
                            '25cc+0302' => _vips('fallend'),
                            '25cc+1dc4' => _vips('hoch steigend'),
                            '25cc+1dc5' => _vips('niedrig steigend'),
                            '25cc+1dc8' => _vips('steigend-fallend')
                        ]
                    ],
                    [
                        'chars' => [
                            '2190' => _vips('kommt von'),
                            '2192' => _vips('wird zu'),
                            '02bc' => _vips('ausstoßend'),
                            '25cc+032a' => _vips('dental'),
                            '25cc+033a' => _vips('apikal'),
                            '25cc+0339' => _vips('mehr gerundet'),
                            '25cc+031c' => _vips('weniger gerundet'),
                            '25cc+033d' => _vips('zur Mitte zentralisiert'),
                            '25cc+032f' => _vips('unsiblisch'),
                            '25cc+0324' => _vips('behaucht'),
                            '25cc+0330' => _vips('knarzig'),
                            '25cc+033c' => _vips('linguolabial'),
                            '02e0' => _vips('velarisiert'),
                            '02e4' => _vips('pharyngalisiert'),
                            '207f' => _vips('Velum wird gesenkt, Luft entweicht durch die Nase'),
                            '02e1' => _vips('Zungenverschluss wird seitlich gelöst'),
                            '02b1' => _vips('angehaucht aspiriert'),
                            '25cc+0318' => _vips('vorgelagerte Zungenwurzel'),
                            '25cc+0319' => _vips('zurückverlagerte Zungenwurzel'),
                            '25cc+033b' => _vips('laminal'),
                            '25cc+031a' => _vips('keine Verschlusslösung hörbar')
                        ]
                    ],
                    [
                        'chars' => [
                            '02e5' => _vips('extra hoch'),
                            '02e6' => _vips('hoch'),
                            '02e7' => _vips('mittelhoch'),
                            '02e8' => _vips('niedrig'),
                            '02e9' => _vips('extra niedrig'),
                            '02e9+200b+02e5' => _vips('steigend'),
                            '02e5+02e9' => _vips('fallend'),
                            '02e6+02e5' => _vips('hoch steigend'),
                            '02e9+02e8' => _vips('niedrig steigend'),
                            '02e7+02e6+02e7' => _vips('steigend-fallend'),
                            '2193' => _vips('Downstep'),
                            '2191' => _vips('Upstep'),
                            '2197' => _vips('globaler Anstieg'),
                            '2198' => _vips('globaler Abfall')
                        ]
                    ],
                    [
                        'chars' => [
                            '0298' => _vips('bilabialer Klick'),
                            '01c0' => _vips('dentaler Klick'),
                            '01c3' => _vips('retroflexer Klick'),
                            '01c2' => _vips('palatoalveolarer Klick'),
                            '01c1' => _vips('lateraler alveolarer Klick')
                        ]
                    ],
                    [
                        'chars' => [
                            '25cc+0323' => _vips('Silbengelenk')
                        ]
                    ]
                ]
            ],

            'german' => [  // german
                'name'       => 'german',
                'title'      => _vips('Deutsche Sonderzeichen'),
                'characters' => [
                    [
                        'title' => 'A',
                        'chars' => [
                            '00c4' => _vips('großes A mit Trema'),
                            '00e4' => _vips('kleines a mit Trema')
                        ]
                    ],
                    [
                        'title' => 'O',
                        'chars' => [
                            '00d6' => _vips('großes O mit Trema'),
                            '00f6' => _vips('kleines o mit Trema')
                        ]
                    ],
                    [
                        'title' => 'S',
                        'chars' => [
                            '00df' => _vips('kleines Eszett'),
                        ]
                    ],
                    [
                        'title' => 'U',
                        'chars' => [
                            '00dc' => _vips('großes U mit Trema'),
                            '00fc' => _vips('kleines u mit Trema')
                        ]
                    ],
                    [
                        'chars' => [
                            '201e' => _vips('öffnende Anführungszeichen'),
                            '201c' => _vips('schließende Anführungszeichen'),
                            '201a' => _vips('öffnende einfache Anführungszeichen'),
                            '2018' => _vips('schließende einfache Anführungszeichen')
                        ]
                    ]
                ]
            ],

            'french' => [  // french
                'name'       => 'french',
                'title'      => _vips('Französische Sonderzeichen'),
                'characters' => [
                    [
                        'title' => 'A',
                        'chars' => [
                            '00c0' => _vips('großes A mit Gravis'),
                            '00c1' => _vips('großes A mit Akut'),
                            '00c2' => _vips('großes A mit Zirkumflex'),
                            '00c6' => _vips('Ligatur aus großem A und großem E'),
                            '00e0' => _vips('kleines a mit Gravis'),
                            '00e1' => _vips('kleines a mit Akut'),
                            '00e2' => _vips('kleines a mit Zirkumflex'),
                            '00e6' => _vips('Ligatur aus kleinem a und kleinem e')
                        ]
                    ],
                    [
                        'title' => 'C',
                        'chars' => [
                            '00c7' => _vips('großes C mit Cedille'),
                            '00e7' => _vips('kleines c mit Cedille')
                        ]
                    ],
                    [
                        'title' => 'E',
                        'chars' => [
                            '00c8' => _vips('großes E mit Gravis'),
                            '00c9' => _vips('großes E mit Akut'),
                            '00ca' => _vips('großes E mit Zirkumflex'),
                            '00cb' => _vips('großes E mit Trema'),
                            '00e8' => _vips('kleines e mit Gravis'),
                            '00e9' => _vips('kleines e mit Akut'),
                            '00ea' => _vips('kleines e mit Zirkumflex'),
                            '00eb' => _vips('kleines e mit Trema')
                        ]
                    ],
                    [
                        'title' => 'I',
                        'chars' => [
                            '00ce' => _vips('großes I mit Zirkumflex'),
                            '00cf' => _vips('großes I mit Trema'),
                            '00ee' => _vips('kleines i mit Zirkumflex'),
                            '00ef' => _vips('kleines i mit Trema')
                        ]
                    ],
                    [
                        'title' => 'O',
                        'chars' => [
                            '00d4' => _vips('großes O mit Zirkumflex'),
                            '0152' => _vips('Ligatur aus großem O und großem E'),
                            '00f4' => _vips('kleines o mit Zirkumflex'),
                            '0153' => _vips('Ligatur aus kleinem o und kleinem e')
                        ]
                    ],
                    [
                        'title' => 'U',
                        'chars' => [
                            '00d9' => _vips('großes U mit Gravis'),
                            '00db' => _vips('großes U mit Zirkumflex'),
                            '00dc' => _vips('großes U mit Trema'),
                            '00f9' => _vips('kleines u mit Gravis'),
                            '00fb' => _vips('kleines u mit Zirkumflex'),
                            '00fc' => _vips('kleines u mit Trema')
                        ]
                    ],
                    [
                        'title' => 'Y',
                        'chars' => [
                            '0178' => _vips('großes Y mit Trema'),
                            '00ff' => _vips('kleines y mit Trema')
                        ]
                    ],
                    [
                        'chars' => [
                            '00ab' => _vips('öffnende Guillemets'),
                            '00bb' => _vips('schließende Guillemets'),
                            '2039' => _vips('öffnende einfache Guillemets'),
                            '203a' => _vips('schließende einfache Guillemets')
                        ]
                    ]
                ]
            ],

            'spanish' => [  // spanish
                'name'       => 'spanish',
                'title'      => _vips('Spanische Sonderzeichen'),
                'characters' => [
                    [
                        'title' => 'A',
                        'chars' => [
                            '00c1' => _vips('großes A mit Akut'),
                            '00e1' => _vips('kleines a mit Akut')
                        ]
                    ],
                    [
                        'title' => 'C',
                        'chars' => [
                            '00e7' => _vips('kleines c mit Cedille')
                        ]
                    ],
                    [
                        'title' => 'E',
                        'chars' => [
                            '00c9' => _vips('großes E mit Akut'),
                            '00e9' => _vips('kleines e mit Akut')
                        ]
                    ],
                    [
                        'title' => 'I',
                        'chars' => [
                            '00cd' => _vips('großes I mit Akut'),
                            '00ed' => _vips('kleines i mit Akut')
                        ]
                    ],
                    [
                        'title' => 'N',
                        'chars' => [
                            '00d1' => _vips('großes N mit Tilde'),
                            '00f1' => _vips('kleines n mit Tilde')
                        ]
                    ],
                    [
                        'title' => 'O',
                        'chars' => [
                            '00d3' => _vips('großes O mit Akut'),
                            '00f3' => _vips('kleines o mit Akut')
                        ]
                    ],
                    [
                        'title' => 'U',
                        'chars' => [
                            '00da' => _vips('großes U mit Akut'),
                            '00fa' => _vips('kleines u mit Akut'),
                            '00fc' => _vips('kleines u mit Trema')
                        ]
                    ],
                    [
                        'chars' => [
                            '00aa' => _vips('weibliche Ordnungszahl'),
                            '00ba' => _vips('männliche Ordnungszahl'),
                            '00a1' => _vips('umgekehrtes Ausrufezeichen'),
                            '00bf' => _vips('umgekehrtes Fragezeichen')
                        ]
                    ],
                    [
                        'chars' => [
                            '00ab' => _vips('öffnende Guillemets'),
                            '00bb' => _vips('schließende Guillemets'),
                            '2039' => _vips('öffnende einfache Guillemets'),
                            '203a' => _vips('schließende einfache Guillemets')
                        ]
                    ]
                ]
            ],

            'portuguese' => [  // portuguese
                'name' => 'portuguese',
                'title' => _vips('Portugiesische Sonderzeichen'),
                'characters' => [
                    [
                        'title' => 'A',
                        'chars' => [
                            '00c0' => _vips('großes A mit Gravis'),
                            '00c1' => _vips('großes A mit Akut'),
                            '00c2' => _vips('großes A mit Zirkumflex'),
                            '00c3' => _vips('großes A mit Tilde'),
                            '00e0' => _vips('kleines a mit Gravis'),
                            '00e1' => _vips('kleines a mit Akut'),
                            '00e2' => _vips('kleines a mit Zirkumflex'),
                            '00e3' => _vips('kleines a mit Tilde')
                        ]
                    ],
                    [
                        'title' => 'C',
                        'chars' => [
                            '00c7' => _vips('großes C mit Cedille'),
                            '00e7' => _vips('kleines c mit Cedille')
                        ]
                    ],
                    [
                        'title' => 'E',
                        'chars' => [
                            '00c9' => _vips('großes E mit Akut'),
                            '00ca' => _vips('großes E mit Zirkumflex'),
                            '00e9' => _vips('kleines e mit Akut'),
                            '00ea' => _vips('kleines e mit Zirkumflex')
                        ]
                    ],
                    [
                        'title' => 'I',
                        'chars' => [
                            '00cd' => _vips('großes I mit Akut'),
                            '00ed' => _vips('kleines i mit Akut')
                        ]
                    ],
                    [
                        'title' => 'O',
                        'chars' => [
                            '00d3' => _vips('großes O mit Akut'),
                            '00d4' => _vips('großes O mit Zirkumflex'),
                            '00d5' => _vips('großes O mit Tilde'),
                            '00f3' => _vips('kleines o mit Akut'),
                            '00f4' => _vips('kleines o mit Zirkumflex'),
                            '00f5' => _vips('kleines o mit Tilde')
                        ]
                    ],
                    [
                        'title' => 'U',
                        'chars' => [
                            '00da' => _vips('großes U mit Akut'),
                            '00dc' => _vips('großes U mit Trema'),
                            '00fa' => _vips('kleines u mit Akut'),
                            '00fc' => _vips('kleines u mit Trema')
                        ]
                    ],
                    [
                        'chars' => [
                            '201c' => _vips('öffnende Anführungszeichen'),
                            '201d' => _vips('schließende Anführungszeichen')
                        ]
                    ]
                ]
            ],

            'romanian' => [  // romanian
                'name' => 'romanian',
                'title' => _vips('Rumänische Sonderzeichen'),
                'characters' => [
                    [
                        'title' => 'A',
                        'chars' => [
                            '00c2' => _vips('großes A mit Zirkumflex'),
                            '0102' => _vips('großes A mit Breve'),
                            '00e2' => _vips('kleines a mit Zirkumflex'),
                            '0103' => _vips('kleines A mit Breve')
                        ]
                    ],
                    [
                        'title' => 'I',
                        'chars' => [
                            '00ce' => _vips('großes I mit Zirkumflex'),
                            '00ee' => _vips('kleines i mit Zirkumflex')
                        ]
                    ],
                    [
                        'title' => 'S',
                        'chars' => [
                            '0218' => _vips('großes S mit untergesetztem Komma'),
                            '0219' => _vips('kleines s mit untergesetztem Komma')
                        ]
                    ],
                    [
                        'title' => 'T',
                        'chars' => [
                            '021a' => _vips('großes T mit untergesetztem Komma'),
                            '021b' => _vips('kleines t mit untergesetztem Komma')
                        ]
                    ],
                    [
                        'chars' => [
                            '201e' => _vips('öffnende Anführungszeichen'),
                            '201d' => _vips('schließende Anführungszeichen'),
                            '00ab' => _vips('öffnende Guillemets'),
                            '00bb' => _vips('schließende Guillemets')
                        ]
                    ]
                ]
            ]
        ];
    }
}

?>
