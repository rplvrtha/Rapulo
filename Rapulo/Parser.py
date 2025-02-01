def parse(token):
    if len(token) != 2:
        raise Exception("Jumlah token tidak valid")
    if token[0].jenis != "KATA_KUNCI":
        raise Exception("Perintah harus 'echo' atau 'print'")
    if token[1].jenis != "STRING":
        raise Exception("Argumen harus berupa string")
    return token[1].nilai  # Mengembalikan string yang akan dicetak