import sys
import re
from Rapulo.Lexer import Token, tokenize
from Rapulo.Parser import parse
from Rapulo.Interpreter import interpret

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Penggunaan: python main.py <nama_file.rpl>")
        sys.exit(1)

    nama_file = sys.argv[1]

    try:
        with open(nama_file, "r") as file:
            kode_sumber = file.read()
    except FileNotFoundError:
        print(f"File tidak ditemukan: {nama_file}")
        sys.exit(1)

    try:
        token = tokenize(kode_sumber)
        string = parse(token)
        interpret(string)
    except Exception as e:
        print(f"Terjadi kesalahan: {e}")