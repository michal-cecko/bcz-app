Potreboval by som aplikáciu na cvičenie. Ktorá by bola napojená na dashboard adminka.synapps.sk, je to filamentphp + laravel dashboard, tam by som vedel niečo spravovať. 
Ale len ako admin. Potreboval by som to spraviť ako SaaS. Každý user by mal bud status free (moji známi) alebo 30d free bez nutnosti zadania karty.
Po 30d by ich nepustilo v ich účte ďalej bez vybrania plánu. (ročné / mesačné sub.)
Registrácia je samozrejme free, ale ak niekto vie, môže si plán vybrať už pri registrácii.

Bol som na tréningu a vytáča ma chaos vo vymýšľaní tréningu, mna nebavi robiť stale rovnake treningy dokola a niekedy niektoré cviky ma ani nenapadajú čo by som mohol robiť. Chcel by som, aby som mal databázu cvikov.
Admin by vedel napopulatovaať databázu cvikov, vedel by spravovať financovanie zákazníkov a videl by takisto tréningy a všetko čo sa týka aj jednotlivých zákazníkov, ako majú povytvárané ich "tréningy", "gymy" atď...

Gyms/Playgrounds: človek by vedel vytvárať alebo používať existujúce globálne gymy. Každý gym má miesto, adresu, (lokalitu na mape) názov, stroje a miestnosti, náčinie (rozumej tagy). Keď tak link na fitko.
Cviky: Cviky by obsahovali teda svoje nejaké requirements. Napríklad na mŕtvy ťah s olympijskou osou potrebuješ olympijsku os a kotúče. Tak ak by tento gym nemal tieto veci (gymu sa dá nastaviť explicitne čo má a čo určite nemá. Ak to nemá, možno to môže byť len chyba že to nemá, pritom to má, takže by som následne výber cvikov nechal len ako odporúčanie že v tom gyme to asi nedocvičíš ale však kľudne si to pridaj do tréningu...)
Cviky budú takisto zamerané na rôzne partie tela: chrbát, triceps, biceps... atď.
Cviky môžu mať aj nastavenú intenzitu. Napr. si môže používateľ nastaviť RPE na cvik že pôjde 4 opakovania. Ak by šiel 6 cvikov po 4 opakovania a je to jeho 100% effort, malo by mu to navrhovať že takýmto tempom sa odfajčí keď takto pôjde dlhodobo. Samozrejme ked si dá taký jednorázový tréning je to OK, ale dlhodobo je to neudržateľné.
Takže človek si vie naplánovať X tréningov do týždňa, napr. Chrbát a triceps alebo Nohy + ramená alebo akékoľvek... A podľa toho si bude vedieť vytvárať tréningy.
Každý cvik môže byť vykonávaný aj rôznym spôsobom, napr. EMOM (every minute on minute), AMRAP, alebo počet sérii a v každej X opakovaní / sekúnd
každý cvik si používateľ môže nastaviť koľko opakovaní, akú váhu, koľko sekúnd chce zvládnuť...
Používateľ si môže nastaviť volitne aj pauzy medzi cvikmi.

Takže appka by mala vedieť toto:

    Scenár 1: 
        Používateľ sa prihlási, vytvorí si tréning v Trnavskom gyme, pôjde cvičiť partiu nohy. 
        Povyberá si tam cviky ALEBO využije AI chata že chce cvičiť nohy atď... a navrhlo by mu to rôzne cviky ktoré by si jedným klikom mohol prihodiť do tréningu.
        Na tréning si môže vybrať "Vedený" štýl - každá séria / cvik, každá pauza medzi sériou sa mu odpočítava in progress. Buď môže mať timer alebo môže iba posúvať Ďalej / ako taký wizard steps :D
        Alebo si vyberie uvoľnený štýl, to znamená že chce vidieť len aký má dnes tréning, aké cviky a ak chce môže zapisovať reps / sets ktoré naozaj dal alebo nemusí. 
        Nakonci tréningu preklikne na "Ukončiť tréning" a tréning bude mať dokončený.

    Scenár 2:
        Používateľ sa prihlási, vytvorí si tréningový plán. Každý tréning sa môže odohrávať inde, môže sa opakovať čo X dní po sebe. Napr. tréningový plán bude na 9 dní.
        Pozadáva si tréningy, ktoré sa majú opakovať, pre každý vyberie miesto, cviky, preferovanú intenzitu tréningu atď...
        A každý tréning ide rovnako ako scenár 1. Na úvodnej obrazovke aplikácie by videl svoj dnešný tréning...



