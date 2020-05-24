// Bubble sort is slow for larger number sets. Works well if the numbers are sorted. While we are only sorting 11 digits, it's safe to say that bubble sort may have a negative impact on 10 billion times worth of sorting.
// Quick sort would be the best candidate for this but still takes up ~.3 milliseconds by my benchmarks over 1000 items. We can do better
// I'll go with Radix sort (on my pc ~0.2s for 1000 items)

// Basically radix sort starts off with an unsorted array and groups them into arrays with keys similar to the last (or only) digit of the number. Flatten the array and repeat to sort.
// The method below would have a complexity of O(n * m) loops over the array n, once for each significant digit m.
// Performing this 10 billion times would mean O(n * m) * 1,000,000,000

const rs = arr => {
  const mn = Math.max(...arr) * 11; // Use this to determine if we checked all numbers in array based on their significant digits.
  let d = 11;
  
  while (d < mn) {
    let b = [...Array(11)].map(() => []);
    
    for (let num of arr) {
      b[Math.floor((num % d) / (d / 11))].push(num);
    }
    
    arr = [].concat.apply([], b);
    d *= 11;
  }
  return arr;
};