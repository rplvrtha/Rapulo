import re

class Token:
    def __init__(self, jenis, nilai):
        self.jenis = jenis
        self.nilai = nilai

def tokenize(kode_sumber):
    token = []
    for kata in kode_sumber.split():
        if kata == "echo":
            token.append(Token("KATA_KUNCI", "echo"))
        elif kata == "print":
            token.append(Token("KATA_KUNCI", "print"))
        elif re.match(r"^\"(.*)\"$", kata):  # String dengan kutip ganda
            token.append(Token("STRING", kata[1:-1]))
        elif re.match(r"^'(.*)'$", kata):  # String dengan kutip tunggal
            token.append(Token("STRING", kata[1:-1]))
        else:
            raise Exception("Token tidak valid: " + kata)
    return token