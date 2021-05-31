DROP TABLE Wyzwanie;
DROP TABLE Aktywnosc;
DROP TABLE Znajomi;
DROP TABLE  Konto;
DROP TABLE Typ_aktywnosci;


CREATE TABLE Konto
(
    id    number GENERATED BY DEFAULT ON NULL AS IDENTITY primary key ,
    login VARCHAR2(20)  unique not null ,
    haslo VARCHAR2(20) not null ,
    plec NUMBER ,
    waga NUMBER,
    wzrost NUMBER,
    zgoda_ranking NUMBER
);

CREATE TABLE Znajomi
(
    znajomy1 REFERENCES Konto,
    znajomy2 REFERENCES Konto,
    CONSTRAINT rozni CHECK ( znajomy1 <> znajomy2 )
);

DROP TABLE ZAPROSZENIA_DO_ZNAJOMYCH;
CREATE TABLE ZAPROSZENIA_DO_ZNAJOMYCH
(
    zapraszajacy REFERENCES Konto,
    zaproszony REFERENCES Konto,
    CONSTRAINT rozni_zaproszenia CHECK ( zapraszajacy <> zaproszony )
);


CREATE TABLE Typ_aktywnosci
(
    id number primary key,
    nazwa varchar2(20)
);


CREATE TABLE Wyzwanie
(
    id number GENERATED BY DEFAULT ON NULL AS IDENTITY primary key,
    nazwa VARCHAR2(20) not null,
    tworca REFERENCES Konto,
    czas_rozpoczecia DATE not null,
    czas_ukonczenia DATE,
    postep NUMBER not null ,
    cel NUMBER not null ,
    jednostka_celu VARCHAR2(20),
    czy_prywatne NUMBER DEFAULT 0,
    id_aktywnosci NUMBER REFERENCES Typ_aktywnosci
);

ALTER TABLE WYZWANIE
ADD CONSTRAINT unikalnosc UNIQUE (nazwa,tworca,czas_rozpoczecia,czas_ukonczenia,id_aktywnosci,jednostka_celu,cel);
commit;

CREATE TABLE ZAPROSZENIE_DO_WYZWANIA
(
    wyzwanie number REFERENCES WYZWANIE,
    zapraszajacy REFERENCES Konto,
    zaproszony REFERENCES Konto,
    CONSTRAINT rozni_zaproszenie_wyzwanie CHECK ( zapraszajacy <> zaproszony )
);




CREATE TABLE UCZESTNICY_WYZWANIA
(
    wyzwanie number REFERENCES WYZWANIE,
    uczestnik number REFERENCES Konto
);
ALTER TABLE UCZESTNICY_WYZWANIA
ADD CONSTRAINT unikalnosc_uczestnicy UNIQUE (wyzwanie,uczestnik);
commit;

CREATE TABLE Aktywnosc
(
    id number REFERENCES Konto,
    id_rodzaju number REFERENCES Typ_aktywnosci,
    ilosc number not null,
    data_rozpoczecia DATE not null ,
    czas_trwania number not null
);

ALTER TABLE AKTYWNOSC
ADD kcal number DEFAULT  0;

commit ;