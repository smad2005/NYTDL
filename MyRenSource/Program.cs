using System;
using System.IO;
using System.Linq;
using System.Text;

namespace MyRen
{
    internal class Program
    {
        private static void Main(string[] args)
        {
            for (int i = 0; i < args.Length; i++)
            {
                args[i] = Encoding.UTF8.GetString(Encoding.Default.GetBytes(args[i]));
            }
            if (!args.All(String.IsNullOrEmpty))
            {
                try
                {
                    File.Move(args[0], args[1]);
                }
                catch (Exception ex)
                {
                    Console.WriteLine(ex.Message);
                }
            }
            else
            {
                var result = "args: " + String.Concat(args);
                Console.WriteLine(result);
            }
        }
    }
}