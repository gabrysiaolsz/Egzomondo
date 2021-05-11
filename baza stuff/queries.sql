
/*zmienia wszystko oprocz loginu i hasła*/
UPDATE table Konto
SET plec =  ,
waga= ,
wzrost= ,
zgoda_ranking =
WHERE login = '' AND haslo = '';
/*end*/

UPDATE table Konto
SET haslo =  ''
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET plec =  /*num (0 lub 1)*/
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET  waga =
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET wzrost =
WHERE login = '' AND haslo = '';

UPDATE table Konto
SET zgoda_ranking =  /*num (0 lub 1)*/
WHERE login = '' AND haslo = '';


/*znajomi - konwersja do dwukierunkowych*/
SELECT * from ZNAJOMI
UNION
SELECT ZNAJOMY2,ZNAJOMY1 from ZNAJOMI;

SELECT K.LOGIN from KONTO K LEFt JOIN
                  (
                      SELECT *
                      from KONTO
                               LEFT JOIN
                           (SELECT *
                            from ZNAJOMI
                            UNION
                            SELECT ZNAJOMY2, ZNAJOMY1
                            from ZNAJOMI) P
                           ON KONTO.id = P.ZNAJOMY1
                      WHERE KONTO.LOGIN = 'janusz'
                  ) T
ON K.ID = T.ZNAJOMY2 WHERE T.LOGIN is not null;


SELECT LOGIN,NAZWA,SUM(ILOSC) odleglosc,SUM(CZAS_TRWANIA)czas
from (
SELECT K.LOGIN, T.NAZWA, A.ILOSC, A.CZAS_TRWANIA
    from KONTO K
    INNER JOIN AKTYWNOSC A on K.ID = A.ID
    INNER JOIN TYP_AKTYWNOSCI T on t.ID = A.ID
    INNER JOIN UCZESTNICY_WYZWANIA UW on UW.UCZESTNIK = K.ID
    INNER JOIN WYZWANIE W on UW.WYZWANIE = W.ID
    WHERE W.ID = ?
    AND A.DATA_ROZPOCZECIA <= W.CZAS_UKONCZENIA AND A.DATA_ROZPOCZECIA>= W.CZAS_ROZPOCZECIA
) GROUP BY LOGIN, NAZWA;
